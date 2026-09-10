<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Testimonial;
use App\Support\Localization\LocalizedUrl;
use App\Support\Media\MediaUrl;
use App\Support\Pricing\PricingCatalogPresenter;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(
        private readonly FrontendSeoBuilder $seo,
        private readonly PricingCatalogPresenter $pricingCatalogPresenter,
    ) {}

    public function index(): View
    {
        return view('frontend.services.index', $this->listingData() + [
            'seo' => $this->seo->listing(
                'Dịch vụ | '.$this->seo->siteName(),
                'Khám phá các dịch vụ tư vấn, thiết kế, thi công và bảo trì hệ thống PCCC.',
                LocalizedUrl::route('services.index'),
            ),
        ]);
    }

    public function redirectCategory(ServiceCategory $category): RedirectResponse
    {
        abort_unless($category->is_active, 404);

        return redirect()->to(LocalizedUrl::serviceCategory($category), 301);
    }

    public function category(ServiceCategory $category): View
    {
        abort_unless($category->is_active, 404);

        $title = $category->name.' | Dịch vụ';
        $description = $category->description ?: 'Dịch vụ PCCC thuộc nhóm '.$category->name.'.';

        return view('frontend.services.index', $this->listingData($category) + [
            'seo' => $this->seo->listing($title, $description, LocalizedUrl::serviceCategory($category), image: $category->seoImageUrl()),
        ]);
    }

    public function show(Service $service): View
    {
        abort_unless($service->status === 'published' && (! $service->published_at || $service->published_at->isPast()), 404);

        $service->load([
            'category',
            'curatorMedia',
            'bannerVideoMedia',
            'processBackgroundMedia',
            'commitmentMedia',
            'pricingCatalog.sourceMedia',
            'pricingCatalog.packages' => fn ($query) => $query
                ->active()
                ->with(['items' => fn ($itemQuery) => $itemQuery->active()->orderBy('sort_order')])
                ->orderBy('sort_order'),
            'faqs' => fn ($query) => $query->active()->ordered(),
            'approvedComments' => fn ($query) => $query->latest('approved_at')->latest('id'),
            'backstageProjects' => fn ($query) => $query
                ->published()
                ->with(['category', 'curatorMedia'])
                ->orderByDesc('published_at'),
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
            ->with(['category', 'curatorMedia'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(3)
            ->get());

        if ($relatedServices->isEmpty()) {
            $relatedServices = $this->withImages(Service::query()
                ->published()
                ->whereKeyNot($service->id)
                ->with(['category', 'curatorMedia'])
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
        $pricingMatrix = $this->pricingCatalogPresenter->present($service->pricingCatalog);

        return view('frontend.services.show', compact('service') + [
            'relatedServices' => $relatedServices,
            'backstageProjects' => $this->withImages($service->backstageProjects),
            'bannerVideoUrl' => $bannerVideoUrl,
            'bannerVideoType' => $service->bannerVideoMedia?->type,
            'pricingCatalog' => $service->pricingCatalog,
            'pricingMediaUrl' => MediaUrl::resolve($service->pricingCatalog?->sourceMedia),
            'pricingMediaIsImage' => str_starts_with((string) $service->pricingCatalog?->sourceMedia?->type, 'image/'),
            'pricingSourceUrl' => $service->pricingCatalog?->source_url,
            'processBackgroundUrl' => MediaUrl::resolve($service->processBackgroundMedia) ?: $service->image_url,
            'commitmentImageUrl' => MediaUrl::resolve($service->commitmentMedia) ?: $service->image_url,
            'processItems' => $processItems,
            'benefitItems' => $benefitItems,
            'commitmentItems' => $commitmentItems,
            'pricingMatrix' => $pricingMatrix,
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
            ->with(['category', 'curatorMedia']);

        if ($activeCategory) {
            $servicesQuery->where('service_category_id', $activeCategory->id);
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

        $featuredProjects = Project::query()
            ->published()
            ->with(['category', 'curatorMedia'])
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();
        $this->withImages($featuredProjects);

        $processItems = collect($heroService?->process_items ?? [])
            ->filter(fn (mixed $item): bool => is_array($item) && filled($item['title'] ?? null))
            ->take(6)
            ->values();

        $categories = $activeCategory
            ? collect()
            : ServiceCategory::query()
                ->where('is_active', true)
                ->withCount(['services' => fn (Builder $query) => $query->published()])
                ->with(['services' => fn ($query) => $query
                    ->published()
                    ->with(['curatorMedia'])
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get();

        $categories->each(function (ServiceCategory $category): void {
            $featuredService = $category->services->first();
            $category->setAttribute('image_url', $featuredService
                ? MediaUrl::resolve($featuredService->curatorMedia)
                : null);
        });

        return [
            'activeCategory' => $activeCategory,
            'categories' => $categories,
            'services' => $services,
            'heroImageUrl' => $heroService ? MediaUrl::resolve($heroService->curatorMedia) : null,
            'processBackgroundUrl' => $heroService
                ? (MediaUrl::resolve($heroService->processBackgroundMedia) ?: MediaUrl::resolve($heroService->curatorMedia))
                : null,
            'processItems' => $processItems,
            'featuredProjects' => $featuredProjects,
            'archiveStats' => [
                ['value' => (string) Service::query()->published()->count(), 'label' => 'hạng mục dịch vụ'],
                ['value' => (string) Project::query()->published()->count(), 'label' => 'công trình đã triển khai'],
                ['value' => (string) ServiceCategory::query()->where('is_active', true)->count(), 'label' => 'nhóm giải pháp'],
                ['value' => '24/7', 'label' => 'sẵn sàng hỗ trợ'],
            ],
            'pageTitle' => $activeCategory?->name ?? 'Dịch vụ PCCC',
            'pageDescription' => $activeCategory?->description ?: 'Các giải pháp PCCC được xây dựng theo mục tiêu, quy mô và đặc thù riêng của từng công trình.',
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
