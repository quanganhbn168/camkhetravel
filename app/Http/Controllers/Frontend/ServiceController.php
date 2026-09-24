<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Testimonial;
use App\Support\Categories\CategoryTree;
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
            'categoryBodyHtml' => (string) str((string) $category->body)->sanitizeHtml(),
            'seo' => $this->seo->listing($title, $description, route('services.category', ['category' => $category->slug]), image: $category->seoImageUrl()),
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
            'processBackgroundMedia',
            'commitmentMedia',
            'faqs' => fn ($query) => $query->active()->ordered(),
            'approvedComments' => fn ($query) => $query->latest('approved_at')->latest('id'),
        ]);
        $service->setAttribute('image_url', MediaUrl::resolve($service->curatorMedia));
        $service->setAttribute('body_html', (string) $service->body);
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
            ->limit(3)
            ->get());

        if ($relatedServices->isEmpty()) {
            $relatedServices = $this->withImages(Service::query()
                ->published()
                ->whereKeyNot($service->id)
                ->with(['category', 'curatorMedia', 'slugs'])
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->limit(3)
                ->get());
        }

        $backstageGalleryImages = $this->galleryImages($service->backstage_gallery);
        $galleryImages = $this->galleryImages($service->gallery);
        $processItems = $this->mediaItems($service->process_items);
        $benefitItems = $this->mediaItems($service->benefit_items);
        $referenceVideos = $this->videoItems($service->reference_videos);
        $faqItems = $service->faqs
            ->map(fn ($faq): array => [
                'question' => $faq->question,
                'answer' => $faq->answer,
            ])
            ->values();
        $ratedComments = $service->approvedComments
            ->filter(fn ($comment): bool => $comment->rating !== null)
            ->values();
        $commitmentItems = collect($service->commitment_items ?? [])
            ->filter(fn (mixed $item): bool => is_array($item))
            ->map(fn (array $item): array => [
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
            ])
            ->filter(fn (array $item): bool => $item['title'] !== '')
            ->values();
        if ($commitmentItems->isEmpty()) {
            $commitmentItems = collect([
                ['title' => 'Rõ ràng ngay từ đầu', 'description' => 'Điểm đón, thời gian và nhu cầu xe được trao đổi trước chuyến đi.'],
                ['title' => 'Đồng hành theo lịch trình', 'description' => 'Đầu mối liên hệ hỗ trợ khi cần điều chỉnh thông tin chuyến đi.'],
                ['title' => 'Xác nhận phương án xe', 'description' => 'Loại xe và chi phí được thống nhất trước khi khởi hành.'],
            ]);
        }
        $hasReferenceVideos = $referenceVideos !== [];
        $hasReferenceImages = $galleryImages !== [];
        $hasReferenceTabs = $hasReferenceVideos && $hasReferenceImages;
        return view('frontend.services.show', compact('service') + [
            'relatedServices' => $relatedServices,
            'bannerVideoUrl' => $bannerVideoUrl,
            'bannerVideoType' => $service->bannerVideoMedia?->type,
            'processBackgroundUrl' => MediaUrl::resolve($service->processBackgroundMedia) ?: $service->image_url,
            'commitmentImageUrl' => MediaUrl::resolve($service->commitmentMedia) ?: $service->image_url,
            'processItems' => $processItems,
            'benefitItems' => $benefitItems,
            'commitmentItems' => $commitmentItems,
            'testimonials' => Testimonial::query()
                ->active()
                ->with('curatorMedia')
                ->orderBy('sort_order')
                ->limit(6)
                ->get(),
            'statsItems' => collect($service->stats_items ?? [])
                ->filter(fn (mixed $item): bool => is_array($item) && filled($item['label'] ?? null))
                ->values(),
            'referenceVideos' => $referenceVideos,
            'referenceImages' => array_values(array_unique($galleryImages)),
            'backstageImages' => array_values(array_unique($backstageGalleryImages)),
            'hasReferenceVideos' => $hasReferenceVideos,
            'hasReferenceImages' => $hasReferenceImages,
            'hasReferenceTabs' => $hasReferenceTabs,
            'faqItems' => $faqItems,
            'ratingSummary' => [
                'count' => $ratedComments->count(),
                'average' => $ratedComments->isNotEmpty() ? round((float) $ratedComments->avg('rating'), 1) : null,
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

    /** @return list<string> */
    private function galleryImages(?array $gallery): array
    {
        $ids = collect($gallery ?? [])
            ->map(fn (mixed $item): mixed => is_array($item) ? ($item['id'] ?? $item['media_id'] ?? null) : $item)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return [];
        }

        $media = Media::query()->whereKey($ids->all())->get()->keyBy('id');

        return $ids
            ->map(fn (int $id): ?string => $media->get($id)?->url)
            ->filter()
            ->values()
            ->all();
    }

    /** @return list<array<string, mixed>> */
    private function mediaItems(?array $items): array
    {
        $items = collect($items ?? [])
            ->filter(fn (mixed $item): bool => is_array($item))
            ->values();
        $mediaIds = $items
            ->map(fn (array $item): mixed => $item['media_id'] ?? $item['image_id'] ?? null)
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique();
        $media = $mediaIds->isEmpty()
            ? collect()
            : Media::query()->whereKey($mediaIds->all())->get()->keyBy('id');

        return $items
            ->map(function (array $item) use ($media): array {
                $mediaId = is_numeric($item['media_id'] ?? null)
                    ? (int) $item['media_id']
                    : (is_numeric($item['image_id'] ?? null) ? (int) $item['image_id'] : null);
                $item['media_url'] = $mediaId ? $media->get($mediaId)?->url : null;

                return $item;
            })
            ->all();
    }

    /** @return list<array<string, mixed>> */
    private function videoItems(?array $items): array
    {
        return collect($items ?? [])
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['url'] ?? null))
            ->map(fn (array $item): array => [
                'title' => trim((string) ($item['title'] ?? 'Video tham khảo')),
                'url' => trim((string) $item['url']),
                'description' => trim((string) ($item['description'] ?? '')),
                'thumbnail_url' => is_numeric($item['thumbnail_media_id'] ?? null)
                    ? MediaUrl::resolve(Media::query()->find((int) $item['thumbnail_media_id']))
                    : null,
            ])
            ->values()
            ->all();
    }

    private function withImages(iterable $services): iterable
    {
        foreach ($services as $service) {
            $service->setAttribute('image_url', MediaUrl::resolve($service->curatorMedia));
        }

        return $services;
    }
}
