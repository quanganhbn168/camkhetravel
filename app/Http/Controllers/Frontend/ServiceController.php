<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContentItem;
use App\Models\Landing;
use App\Models\LandingCategory;
use App\Models\MediaAsset;
use App\Services\WordPress\WordPressMediaUrlMapper;
use App\Support\Frontend\MediaUrl;
use App\Support\Landing\LandingPageBlocks;
use App\Support\Localization\LocalizedUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(
        private readonly WordPressMediaUrlMapper $mediaUrlMapper,
        private readonly FrontendSeoBuilder $seo,
        private readonly LandingPageBlocks $landingPageBlocks,
    ) {}

    public function index(): View
    {
        return view('frontend.services.index', $this->listingData() + [
            'seo' => $this->seo->listing(
                'Dịch vụ | '.$this->seo->siteName(),
                'Khám phá các dịch vụ truyền thông, sản xuất nội dung và tổ chức sự kiện.',
                LocalizedUrl::route('services.index'),
            ),
        ]);
    }

    public function category(LandingCategory $category): View
    {
        abort_unless($category->is_active, 404);

        $title = $category->name.' | Dịch vụ';
        $description = $category->description ?: 'Dịch vụ truyền thông được '.$category->name.' cung cấp.';

        return view('frontend.services.index', $this->listingData($category) + [
            'seo' => $this->seo->listing($title, $description, LocalizedUrl::slug($category->slug)),
        ]);
    }

    public function show(Landing $service): View
    {
        abort_unless($service->status === 'published' && (! $service->published_at || $service->published_at->isPast()), 404);

        $isNativeLanding = $service->layout_mode === 'custom_template'
            && filled($service->template_key);
        $serviceRelations = [
            'category',
            'curatorMedia',
            'pricingMedia',
            'pricingPlans' => fn ($query) => $query->active()->orderBy('sort_order'),
            'approvedComments' => fn ($query) => $query->latest('approved_at')->latest('id'),
            'backstageProjects' => fn ($query) => $query
                ->published()
                ->with(['category', 'curatorMedia'])
                ->orderByDesc('published_at'),
        ];

        if (! $isNativeLanding) {
            $serviceRelations['legacyContent'] = fn ($query) => $query;
            $serviceRelations['legacyMedia'] = fn ($query) => $query;
            $serviceRelations['backstageProjects'] = fn ($query) => $query
                ->published()
                ->with(['category', 'curatorMedia', 'legacyMedia'])
                ->orderByDesc('published_at');
        }

        $service->load($serviceRelations);
        $legacyContent = $service->relationLoaded('legacyContent') ? $service->legacyContent : null;
        $legacyMedia = $service->relationLoaded('legacyMedia') ? $service->legacyMedia : null;
        $service->setAttribute('image_url', MediaUrl::resolve($service->curatorMedia, $legacyMedia));
        $service->setAttribute('body_html', $isNativeLanding
            ? (string) $service->body
            : $this->mediaUrlMapper->absoluteLocalMediaUrls((string) $service->body));
        $relatedServices = $this->withImages(Landing::query()
            ->published()
            ->whereKeyNot($service->id)
            ->when($service->landing_category_id, fn ($query) => $query->where('landing_category_id', $service->landing_category_id))
            ->with($isNativeLanding ? ['category', 'curatorMedia'] : ['category', 'curatorMedia', 'legacyMedia'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(3)
            ->get());
        if ($relatedServices->isEmpty()) {
            $relatedServices = $this->withImages(Landing::query()
                ->published()
                ->whereKeyNot($service->id)
                ->with($isNativeLanding ? ['category', 'curatorMedia'] : ['category', 'curatorMedia', 'legacyMedia'])
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->limit(3)
                ->get());
        }
        $backstageGalleryImages = $this->galleryImages($service->backstage_gallery);
        $galleryImages = $this->galleryImages($service->gallery);
        $faqItems = collect($service->faq_items ?? [])
            ->map(fn (mixed $item): array => [
                'question' => trim((string) (is_array($item) ? ($item['question'] ?? '') : '')),
                'answer' => trim((string) (is_array($item) ? ($item['answer'] ?? '') : '')),
            ])
            ->filter(fn (array $item): bool => $item['question'] !== '' && $item['answer'] !== '')
            ->values();
        $ratedComments = $service->approvedComments
            ->filter(fn ($comment): bool => $comment->rating !== null)
            ->values();
        $usesBuilderLayout = in_array($service->layout_mode, ['builder', 'custom_template'], true)
            && collect($service->sections)->isNotEmpty();

        return view('frontend.services.show', compact('service') + [
            'relatedServices' => $relatedServices,
            'usesLandingLayout' => true,
            'isLegacyLanding' => $legacyContent?->type === 'landing',
            'backstageProjects' => $this->withImages($service->backstageProjects),
            'pricingMediaUrl' => $service->pricingMedia?->url,
            'pricingMediaIsImage' => str_starts_with((string) $service->pricingMedia?->type, 'image/'),
            'introMediaUrl' => $service->pricingMedia?->url ?: $service->image_url,
            'introMediaIsPrice' => $service->pricingMedia !== null,
            'referenceVideos' => $isNativeLanding ? [] : $this->referenceVideos($legacyContent),
            'referenceImages' => array_values(array_unique([...$galleryImages, ...$backstageGalleryImages])),
            'faqItems' => $faqItems,
            'ratingSummary' => [
                'count' => $ratedComments->count(),
                'average' => $ratedComments->isNotEmpty() ? round((float) $ratedComments->avg('rating'), 1) : null,
            ],
            'serviceVideoUrl' => null,
            'usesBuilderLayout' => $usesBuilderLayout,
            'landingBlocks' => $usesBuilderLayout ? $this->landingPageBlocks->prepare($service) : [],
            'landingTheme' => $this->landingPageBlocks->theme($service),
            'landingTemplateView' => $this->landingPageBlocks->templateView($service),
            'landingTemplateDefinition' => $this->landingPageBlocks->templateDefinition($service),
            'landingTemplateSettings' => $this->landingPageBlocks->templateSettings($service),
            'landingTemplateMedia' => $this->landingPageBlocks->templateMedia($service),
            'landingCampaignState' => $this->landingPageBlocks->campaignState($service),
            'landingTrackingUrl' => route('landings.track', ['landing' => $service->id]),
            'hideHeader' => $usesBuilderLayout && ! $service->show_header,
            'hideFooter' => $usesBuilderLayout && ! $service->show_footer,
            'seo' => $this->seo->landing($service),
        ]);
    }

    public function legacyService(string $slug): RedirectResponse
    {
        $service = Landing::query()
            ->published()
            ->whereHas('legacyContent', fn (Builder $query) => $query
                ->where('source', 'wordpress')
                ->where('slug', $slug)
                ->whereIn('type', ['service', 'landing']))
            ->firstOrFail();

        return redirect()->to(LocalizedUrl::slug($service->slug), 301);
    }

    /** @return array<string, mixed> */
    private function listingData(?LandingCategory $activeCategory = null): array
    {
        $sort = request()->string('sort')->value();
        $sort = in_array($sort, ['latest', 'featured', 'title'], true) ? $sort : 'latest';

        $servicesQuery = Landing::query()
            ->published()
            ->with(['category', 'curatorMedia', 'legacyMedia']);

        if ($activeCategory) {
            $servicesQuery->where('landing_category_id', $activeCategory->id);
        }

        $this->applyOrdering($servicesQuery, $sort);

        $services = $this->withImages($servicesQuery
            ->paginate(12)
            ->withQueryString());

        $heroService = Landing::query()
            ->published()
            ->when($activeCategory, fn (Builder $query) => $query->where('landing_category_id', $activeCategory->id))
            ->with(['curatorMedia', 'legacyMedia'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->first();

        $categories = $activeCategory
            ? collect()
            : LandingCategory::query()
                ->where('is_active', true)
                ->withCount(['landings' => fn (Builder $query) => $query->published()])
                ->with(['landings' => fn ($query) => $query
                    ->published()
                    ->with(['curatorMedia', 'legacyMedia'])
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get();

        $categories->each(function (LandingCategory $category): void {
            $featuredService = $category->landings->first();

            $category->setAttribute('image_url', $featuredService
                ? MediaUrl::resolve($featuredService->curatorMedia, $featuredService->legacyMedia)
                : null);
        });

        return [
            'activeCategory' => $activeCategory,
            'categories' => $categories,
            'services' => $services,
            'heroImageUrl' => $heroService ? MediaUrl::resolve($heroService->curatorMedia, $heroService->legacyMedia) : null,
            'pageTitle' => $activeCategory?->name ?? 'Dịch vụ',
            'pageDescription' => $activeCategory?->description ?: 'Các giải pháp truyền thông được xây dựng theo mục tiêu, nguồn lực và ngữ cảnh riêng của từng thương hiệu.',
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

    /** @return list<array{id: int, title: string, url: string, thumbnail_url: ?string}> */
    private function referenceVideos(?ContentItem $legacyContent): array
    {
        $sourceIds = collect($legacyContent?->legacy_meta['ladi_video_sellect'] ?? [])
            ->flatten()
            ->filter(fn (mixed $id): bool => is_numeric($id))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values();

        if ($sourceIds->isEmpty()) {
            return [];
        }

        $videos = ContentItem::query()
            ->where('source', 'wordpress')
            ->whereIn('source_id', $sourceIds)
            ->get()
            ->keyBy('source_id');
        $thumbnailSourceIds = $videos
            ->map(fn (ContentItem $video): int => (int) collect($video->legacy_meta['_thumbnail_id'] ?? [])->flatten()->first())
            ->filter()
            ->unique()
            ->values();
        $pathsBySourceId = MediaAsset::query()
            ->where('source', 'wordpress')
            ->whereIn('source_id', $thumbnailSourceIds)
            ->pluck('file_path', 'source_id');
        $mediaByPath = Media::query()
            ->where('disk', 'public')
            ->whereIn('path', $pathsBySourceId->filter()->values())
            ->get()
            ->keyBy('path');

        return $sourceIds
            ->map(function (int $sourceId) use ($videos, $pathsBySourceId, $mediaByPath): ?array {
                $video = $videos->get($sourceId);
                $url = collect($video?->legacy_meta['url_video_youtube'] ?? [])
                    ->flatten()
                    ->first(fn (mixed $value): bool => is_string($value) && filled($value));

                if (! $video || ! $url) {
                    return null;
                }

                $thumbnailId = (int) collect($video->legacy_meta['_thumbnail_id'] ?? [])->flatten()->first();

                return [
                    'id' => $sourceId,
                    'title' => $video->title,
                    'url' => $url,
                    'thumbnail_url' => $mediaByPath->get($pathsBySourceId->get($thumbnailId))?->url,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function withImages(iterable $services): iterable
    {
        foreach ($services as $service) {
            $legacyMedia = $service->relationLoaded('legacyMedia') ? $service->legacyMedia : null;
            $service->setAttribute('image_url', MediaUrl::resolve($service->curatorMedia, $legacyMedia));
        }

        return $services;
    }
}
