<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Support\Categories\CategoryTree;
use App\Settings\ServicePageSettings;
use App\Support\Services\ServiceIcons;
use App\Support\Content\ArticleOutline;
use App\Support\Media\MediaUrl;
use App\Support\Pages\SystemPageProfileResolver;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly SystemPageProfileResolver $systemPages,
    ) {}

    public function index(): View
    {
        $page = $this->systemPages->require('services');
        $data = $this->listingData();
        $data['page'] = $page;
        $data['pageTitle'] = $page['title'];
        $data['pageBannerUrl'] = $page['banner_url'];
        $data['seo'] = $this->seo->systemPage($page, 'services.index');

        return view('frontend.services.index', $data);
    }

    public function redirectCategory(ServiceCategory $category): RedirectResponse
    {
        abort_unless($category->is_active, 404);
        $category->loadMissing('slugs');

        return redirect()->route('services.category', ['category' => $category->slug], 301);
    }

    public function category(ServiceCategory $category): View
    {
        abort_unless($category->is_active, 404);
        $category->loadMissing('slugs');

        $title = $category->seo_title ?: $category->name.' | Dịch vụ';
        $description = $category->seo_description ?: $category->description ?: 'Tìm hiểu dịch vụ thuộc nhóm '.$category->name.' của CamKheTravel.';

        return view('frontend.services.index', $this->listingData($category) + [
            'categoryBodyHtml' => ArticleOutline::prepare((string) str((string) $category->body)->sanitizeHtml())['html'],
            'seo' => $this->seo->listing($title, $description, route('services.category', ['category' => $category->slug]), image: $category->seoImageUrl(), trail: [['name' => 'Dịch vụ', 'url' => route('services.index')], ['name' => $category->name, 'url' => route('services.category', ['category' => $category->slug])]]),
        ]);
    }

    public function show(Service $service): View
    {
        abort_unless($service->status === 'published' && (! $service->published_at || $service->published_at->isPast()), 404);

        $service->load([
            'category.slugs',
            'curatorMedia',
            'slugs',
            'bannerVideoMedia',
            'areaMedia',
            'faqs' => fn ($query) => $query->active()->ordered(),
            'approvedComments' => fn ($query) => $query->latest('approved_at')->latest('id'),
        ]);
        $service->setAttribute('image_url', MediaUrl::resolve($service->curatorMedia));
        $content = ArticleOutline::prepare((string) $service->body);
        $service->setAttribute('body_html', $content['html']);
        $bannerVideoUrl = str_starts_with((string) $service->bannerVideoMedia?->type, 'video/')
            ? MediaUrl::resolve($service->bannerVideoMedia)
            : null;

        $relatedServices = $this->withImages(Service::query()
            ->published()
            ->whereKeyNot($service->id)
            ->when($service->service_category_id, fn ($query) => $query->where('service_category_id', $service->service_category_id))
            ->with(['category', 'curatorMedia', 'slugs'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(6)
            ->get());

        if ($relatedServices->isEmpty()) {
            $relatedServices = $this->withImages(Service::query()
                ->published()
                ->whereKeyNot($service->id)
                ->with(['category', 'curatorMedia', 'slugs'])
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->limit(6)
                ->get());
        }

        $this->applyServicePageTemplate($service);
        $benefitItems = $this->withIcons($this->mediaItems($service->benefit_items), ['location', 'clock', 'car', 'money', 'headset']);
        $processItems = $this->withIcons($this->mediaItems($service->process_items), ['phone', 'list', 'tag', 'check']);
        $faqItems = $service->faqs
            ->map(fn ($faq): array => [
                'question' => $faq->question,
                'answer' => $faq->answer,
            ])
            ->values();
        $ratedComments = $service->approvedComments
            ->filter(fn ($comment): bool => $comment->rating !== null)
            ->values();

        return view('frontend.services.show', compact('service') + [
            'relatedServices' => $relatedServices,
            'bannerVideoUrl' => $bannerVideoUrl,
            'bannerVideoType' => $service->bannerVideoMedia?->type,
            'hero' => [
                'badge' => $service->hero_badge ?: $service->category?->name,
                'subtitle' => $service->hero_subtitle ?: $service->excerpt,
                'points' => $this->heroPoints($service, $benefitItems),
            ],
            'packageItems' => $this->withIcons(
                collect($service->package_items ?? [])->filter(fn (mixed $item): bool => is_array($item) && filled($item['title'] ?? null))->values()->all(),
                ['users', 'car', 'box'],
            ),
            'pricingGroups' => $this->pricingGroups($service->pricing_groups),
            'processItems' => $processItems,
            'benefitItems' => $benefitItems,
            'usecaseItems' => $this->mediaItems($service->usecase_items),
            'vehicleItems' => $this->mediaItems($service->vehicle_items),
            'areaItems' => collect($service->area_items ?? [])->pluck('title')->filter()->values(),
            'areaImageUrl' => MediaUrl::resolve($service->areaMedia),
            'faqItems' => $faqItems,
            'tableOfContents' => $content['tableOfContents'],
            'ratingSummary' => [
                'count' => $ratedComments->count(),
                'average' => $ratedComments->isNotEmpty() ? round((float) $ratedComments->avg('rating'), 1) : null,
                // Số đánh giá theo từng mức sao, từ 5 xuống 1.
                'distribution' => collect([5, 4, 3, 2, 1])
                    ->mapWithKeys(fn (int $star): array => [$star => $ratedComments->where('rating', $star)->count()])
                    ->all(),
            ],
            'seo' => $this->seo->service($service),
        ]);
    }

    /** @return array<string, mixed> */
    private function listingData(?ServiceCategory $activeCategory = null): array
    {
        $sort = request()->string('sort')->value();
        $sort = in_array($sort, ['latest', 'featured', 'title'], true) ? $sort : 'latest';

        $servicesQuery = Service::query()
            ->published()
            ->with(['category', 'curatorMedia', 'slugs']);

        if ($activeCategory) {
            $servicesQuery->whereIn('service_category_id', $activeCategory->subtreeIds(activeOnly: true));
        }

        $this->applyOrdering($servicesQuery, $sort);

        $services = $this->withImages($servicesQuery
            ->paginate(12)
            ->withQueryString());

        $heroService = Service::query()
            ->published()
            ->when($activeCategory, fn (Builder $query) => $query->where('service_category_id', $activeCategory->id))
            ->with(['curatorMedia', 'processBackgroundMedia'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->first();

        $processItems = collect($heroService?->process_items ?? [])
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['title'] ?? null))
            ->take(6)
            ->values();

        $categories = ServiceCategory::query()
            ->where('is_active', true)
            ->withCount(['services' => fn (Builder $query) => $query->published()])
            ->with(['slugs', 'curatorMedia', 'services' => fn ($query) => $query
                ->published()
                ->with(['curatorMedia', 'slugs'])
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        $categories = CategoryTree::forDisplay($categories, 'services_count');
        $categories->each(fn (ServiceCategory $category) => $category->setAttribute('public_url', route('services.category', ['category' => $category->slug])));

        return [
            'activeCategory' => $activeCategory,
            'pageBannerUrl' => $activeCategory?->banner_url,
            'categoryImageUrl' => $activeCategory?->image_url,
            'categories' => $categories,
            'services' => $services,
            'heroImageUrl' => $heroService ? MediaUrl::resolve($heroService->curatorMedia) : null,
            'processBackgroundUrl' => $heroService
                ? (MediaUrl::resolve($heroService->processBackgroundMedia) ?: MediaUrl::resolve($heroService->curatorMedia))
                : null,
            'processItems' => $processItems,
            'archiveStats' => [
                ['value' => (string) Service::query()->published()->count(), 'label' => 'hạng mục dịch vụ'],
                ['value' => (string) ServiceCategory::query()->where('is_active', true)->count(), 'label' => 'nhóm dịch vụ'],
                ['value' => 'Theo lịch', 'label' => 'tư vấn phương án xe'],
            ],
            'pageTitle' => $activeCategory?->name ?? 'Dịch vụ xe và du lịch',
            'pageDescription' => $activeCategory?->description ?: 'CamKheTravel tư vấn phương án xe theo điểm đón, lịch trình và quy mô đoàn.',
            'sort' => $sort,
            'sortOptions' => [
                'latest' => 'Mới nhất',
                'featured' => 'Nổi bật',
                'title' => 'Tên A–Z',
            ],
        ];
    }

    private function applyOrdering(Builder $query, string $sort): void
    {
        match ($sort) {
            'featured' => $query->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('published_at'),
            'title' => $query->orderBy('title'),
            default => $query->orderByDesc('published_at')->orderByDesc('id'),
        };
    }

    /** @return list<array<string, mixed>> */
    private function mediaItems(?array $items): array
    {
        $items = collect($items ?? [])
            ->filter(fn (mixed $item): bool => is_array($item))
            ->values();
        $mediaIds = $items
            ->map(fn (array $item): ?int => $this->pickedMediaId($item['media_id'] ?? $item['image_id'] ?? null))
            ->filter()
            ->unique();
        $media = $mediaIds->isEmpty()
            ? collect()
            : Media::query()->whereKey($mediaIds->all())->get()->keyBy('id');

        return $items
            ->map(function (array $item) use ($media): array {
                $mediaId = $this->pickedMediaId($item['media_id'] ?? $item['image_id'] ?? null);
                $item['media_url'] = $mediaId ? $media->get($mediaId)?->url : null;

                return $item;
            })
            ->all();
    }

    /**
     * Phần nào dịch vụ để trống (Vì sao chọn, Quy trình, Các dòng xe) thì lấy từ "Mẫu trang dịch vụ".
     * Chỉ gán tạm để hiển thị, không lưu vào dịch vụ.
     */
    private function applyServicePageTemplate(Service $service): void
    {
        $template = app(ServicePageSettings::class);

        foreach (['benefit', 'process', 'vehicle'] as $section) {
            if (! $this->hasItems($service->{$section.'_items'})) {
                $service->setAttribute($section.'_items', $template->{$section.'_items'});
            }
            foreach (['title', 'description'] as $field) {
                $key = $section.'_'.$field;
                if (blank($service->{$key})) {
                    $service->setAttribute($key, ServicePageSettings::withService($template->{$key}, $service->title) ?: null);
                }
            }
        }
    }

    /**
     * Gắn class icon FontAwesome cho từng thẻ; thẻ chưa chọn icon thì lấy lần lượt theo $fallbacks.
     *
     * @param  list<array<string, mixed>>  $items
     * @param  list<string>  $fallbacks
     * @return list<array<string, mixed>>
     */
    private function withIcons(array $items, array $fallbacks): array
    {
        return array_values(array_map(
            fn (array $item, int $index): array => $item + ['icon_class' => ServiceIcons::classFor($item['icon'] ?? null, $fallbacks, $index)],
            $items,
            array_keys($items),
        ));
    }

    /**
     * Các ý có dấu tích ở banner. Repeater "simple" lưu chuỗi; để trống thì lấy 3 lý do đầu của "Vì sao chọn".
     *
     * @param  list<array<string, mixed>>  $benefitItems
     * @return list<string>
     */
    private function heroPoints(Service $service, array $benefitItems): array
    {
        $points = collect($service->hero_points ?? [])
            ->map(fn (mixed $point): string => trim((string) (is_array($point) ? ($point['text'] ?? '') : $point)))
            ->filter()
            ->values();

        return ($points->isNotEmpty() ? $points : collect($benefitItems)->pluck('title')->filter()->take(3))->values()->all();
    }

    /**
     * Bảng giá theo điểm đến; trong mỗi điểm đến, các dòng được gom theo hình thức (bao xe / ghép khách / ...).
     *
     * @return list<array{destination: string, sections: list<array{label: string, icon: string, rows: list<array{label: string, price: string}>}>}>
     */
    private function pricingGroups(?array $groups): array
    {
        $kinds = ServiceIcons::pricingKinds();

        return collect($groups ?? [])
            ->filter(fn (mixed $group): bool => is_array($group) && filled($group['destination'] ?? null))
            ->map(fn (array $group): array => [
                'destination' => trim((string) $group['destination']),
                'sections' => collect($group['rows'] ?? [])
                    ->filter(fn (mixed $row): bool => is_array($row) && filled($row['label'] ?? null))
                    ->groupBy(fn (array $row): string => isset($kinds[$row['kind'] ?? '']) ? $row['kind'] : 'other')
                    ->sortBy(fn ($rows, string $kind): int => array_search($kind, array_keys($kinds), true))
                    ->map(fn ($rows, string $kind): array => [
                        'label' => $kinds[$kind],
                        'icon' => ServiceIcons::pricingKindIcon($kind),
                        'rows' => $rows->map(fn (array $row): array => [
                            'label' => trim((string) $row['label']),
                            'price' => filled($row['price'] ?? null) ? trim((string) $row['price']) : 'Liên hệ',
                        ])->values()->all(),
                    ])
                    ->values()
                    ->all(),
            ])
            ->filter(fn (array $group): bool => $group['sections'] !== [])
            ->values()
            ->all();
    }

    private function hasItems(mixed $items): bool
    {
        return collect(is_array($items) ? $items : [])->contains(fn (mixed $item): bool => is_array($item) && filled($item['title'] ?? null));
    }

    /** ID ảnh do CuratorPicker lưu: thường là số, phòng trường hợp Curator trả về mảng/bản ghi media. */
    private function pickedMediaId(mixed $value): ?int
    {
        if (is_array($value)) {
            $value = $value['id'] ?? (is_array(reset($value)) ? (reset($value)['id'] ?? null) : reset($value));
        }

        return is_numeric($value) ? (int) $value : null;
    }

    private function withImages(iterable $services): iterable
    {
        foreach ($services as $service) {
            $service->setAttribute('image_url', MediaUrl::resolve($service->curatorMedia));
        }

        return $services;
    }
}
