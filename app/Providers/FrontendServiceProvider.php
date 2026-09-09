<?php

namespace App\Providers;

use App\Models\BniActivity;
use App\Models\BniArticle;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniEventSlide;
use App\Models\BniEventVideo;
use App\Models\BniGalleryItem;
use App\Models\HeroSlide;
use App\Models\LandingPage;
use App\Models\Language;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Partner;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\PricingPlan;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Testimonial;
use App\Models\User;
use App\Observers\AssignNextOrderObserver;
use App\Observers\ContentSeoFallbackObserver;
use App\Observers\SlugObserver;
use App\Settings\WebsiteSettings;
use App\Support\Branding\FaviconService;
use App\Support\Localization\LanguageCatalog;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as BladeView;

class FrontendServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LanguageCatalog::class);
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'post' => Post::class,
            'post-category' => PostCategory::class,
            'project' => Project::class,
            'project-category' => ProjectCategory::class,
            'service' => Service::class,
            'service-category' => ServiceCategory::class,
            'landing-page' => LandingPage::class,
            'bni-article' => BniArticle::class,
            'bni-activity' => BniActivity::class,
            'bni-chapter' => BniChapter::class,
            'bni-event' => BniEvent::class,
            'bni-event-slide' => BniEventSlide::class,
            'bni-event-video' => BniEventVideo::class,
            'bni-gallery-item' => BniGalleryItem::class,
            'user' => User::class,
        ]);

        Post::observe(SlugObserver::class);
        PostCategory::observe(SlugObserver::class);
        Project::observe(SlugObserver::class);
        ProjectCategory::observe(SlugObserver::class);
        Service::observe(SlugObserver::class);
        ServiceCategory::observe(SlugObserver::class);
        LandingPage::observe(SlugObserver::class);

        HeroSlide::observe(AssignNextOrderObserver::class);
        Language::observe(AssignNextOrderObserver::class);
        Partner::observe(AssignNextOrderObserver::class);
        PostCategory::observe(AssignNextOrderObserver::class);
        PricingPlan::observe(AssignNextOrderObserver::class);
        Project::observe(AssignNextOrderObserver::class);
        ProjectCategory::observe(AssignNextOrderObserver::class);
        Service::observe(AssignNextOrderObserver::class);
        ServiceCategory::observe(AssignNextOrderObserver::class);
        LandingPage::observe(AssignNextOrderObserver::class);
        Testimonial::observe(AssignNextOrderObserver::class);

        Post::observe(ContentSeoFallbackObserver::class);
        Project::observe(ContentSeoFallbackObserver::class);
        Service::observe(ContentSeoFallbackObserver::class);
        LandingPage::observe(ContentSeoFallbackObserver::class);

        RateLimiter::for('frontend-contact', fn ($request) => Limit::perMinute(5)->by((string) $request->ip()));
        RateLimiter::for('frontend-comment', fn ($request) => Limit::perMinute(3)->by((string) $request->ip()));
        RateLimiter::for('bni-gallery-upload', fn ($request) => Limit::perHour(2)->by((string) $request->ip()));
        RateLimiter::for('landing-tracking', fn ($request) => Limit::perMinute(120)->by(
            (string) data_get($request->route('landingPage'), 'id', $request->route('landingPage')).'|'.(string) $request->ip(),
        ));

        $website = app(WebsiteSettings::class);
        $media = Media::query()
            ->whereIn('id', array_filter([
                $website->logo_media_id,
                $website->seo_image_media_id,
                $website->company_profile_media_id,
                $website->about_image_media_id,
                $website->contact_image_media_id,
                $website->banner_media_id,
                $website->footer_background_media_id,
            ]))
            ->get()
            ->keyBy('id');
        View::share([
            'website' => $website,
            'defaultBannerUrl' => MediaUrl::versioned($media->get($website->banner_media_id)),
            'websiteMedia' => $media,
            'websiteMediaUrls' => $media->mapWithKeys(
                fn (Media $media): array => [$media->getKey() => MediaUrl::versioned($media)],
            ),
            'websiteMediaIcons' => $media->mapWithKeys(fn (Media $media): array => [
                $media->getKey() => [
                    'url' => MediaUrl::versioned($media),
                    'type' => MediaUrl::mimeType($media),
                    'sizes' => MediaUrl::iconSizes($media),
                ],
            ]),
            'faviconLinks' => app(FaviconService::class)->links(),
            'seo' => app(FrontendSeoBuilder::class)->default(),
        ]);

        View::composer('partials.footer', function (BladeView $view) use ($website): void {
            $footerMenu = Menu::query()
                ->where('is_active', true)
                ->with('items')
                ->when(
                    filled($website->footer_menu_id),
                    fn (Builder $query) => $query->whereKey($website->footer_menu_id),
                    fn (Builder $query) => $query->where('location', 'footer'),
                )
                ->first();

            $view->with('footerServices', Service::query()
                ->published()
                ->with('slugs')
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->limit(4)
                ->get(['id', 'title']))
                ->with('footerNavigation', $footerMenu?->items
                    ->whereNull('parent_id')
                    ->map(fn (MenuItem $item): array => $this->menuItemData($item, $footerMenu->items))
                    ->values()
                    ?? collect());
        });

        View::composer('partials.header', function (BladeView $view) use ($media, $website): void {
            $headerMenu = Menu::query()
                ->where('is_active', true)
                ->with('items')
                ->when(
                    filled($website->header_menu_id),
                    fn (Builder $query) => $query->whereKey($website->header_menu_id),
                    fn (Builder $query) => $query->where('location', 'header'),
                )
                ->first();
            $headerNavigation = $headerMenu?->items
                ->whereNull('parent_id')
                ->map(fn (MenuItem $item): array => $this->menuItemData(
                    item: $item,
                    allItems: $headerMenu->items,
                ))
                ->values()
                ?? collect();

            $view->with([
                'headerLogoUrl' => MediaUrl::versioned($media->get($website->logo_media_id)),
                'headerNavigation' => $headerNavigation,
                'headerPhones' => $this->headerPhones($website),
            ]);
        });
    }

    /** @return Collection<int, array{label: string, href: string}> */
    private function headerPhones(WebsiteSettings $website): Collection
    {
        $phones = collect($website->phones ?? [])
            ->filter(fn ($phone) => is_array($phone) && filled($phone['number'] ?? null));

        if ($phones->isEmpty()) {
            $phones = collect([['number' => $website->hotline], ['number' => $website->contact_phone]])
                ->filter(fn ($phone) => filled($phone['number'] ?? null));
        }

        return $phones->map(fn (array $phone): array => [
            'label' => trim($phone['number']),
            'href' => 'tel:'.preg_replace('/\s+/', '', $phone['number']),
        ])->unique('href')->take(2)->values();
    }

    /** @return array{label: string, url: string, target: string, is_active: bool, home: bool, has_children: bool, children: Collection<int, array<string, mixed>>} */
    private function menuItemData(
        MenuItem $item,
        Collection $allItems,
    ): array {
        $link = $item->link;
        $isActive = $this->menuItemMatchesCurrentRoute($item);
        $children = $allItems
            ->where('parent_id', $item->getKey())
            ->map(fn (MenuItem $child): array => $this->menuItemData(
                item: $child,
                allItems: $allItems,
            ))
            ->values();

        return [
            'label' => $item->label,
            'url' => $link,
            'target' => $item->target ?: '_self',
            'is_active' => $isActive || $children->contains('is_active', true),
            'home' => false,
            'has_children' => $children->isNotEmpty(),
            'children' => $children,
        ];
    }

    private function menuItemMatchesCurrentRoute(MenuItem $item): bool
    {
        $linkedSourceType = (string) $item->linked_source_type;

        if ($linkedSourceType === 'native_route') {
            $routeName = trim((string) $item->getRawOriginal('url'));
            $routePattern = match ($routeName) {
                'bni.handover' => 'bni.*',
                'bni.events.index' => 'bni.events.*',
                default => $routeName,
            };

            return $routePattern !== '' && request()->routeIs($routePattern);
        }

        $target = match ($linkedSourceType) {
            'native_service', Service::class, 'service' => [Service::class, 'slug.show'],
            'native_service_category', ServiceCategory::class, 'service-category' => [ServiceCategory::class, 'services.category', 'category'],
            'native_landing_page', LandingPage::class, 'landing-page' => [LandingPage::class, 'slug.show'],
            'native_project', Project::class, 'project' => [Project::class, 'projects.show'],
            'native_project_category', ProjectCategory::class, 'project-category' => [ProjectCategory::class, 'projects.category'],
            'native_post', Post::class, 'post' => [Post::class, 'posts.show'],
            'native_post_category', PostCategory::class, 'post-category' => [PostCategory::class, 'posts.category'],
            default => null,
        };

        if ($target === null || ! request()->routeIs($target[1])) {
            return false;
        }

        $routeParameter = $target[2] ?? 'slug';
        $currentRouteValue = request()->route($routeParameter);
        $currentSlug = $currentRouteValue instanceof Model
            ? (string) ($currentRouteValue->slug ?? $currentRouteValue->getRouteKey())
            : (string) $currentRouteValue;

        if ($currentSlug === '') {
            return false;
        }

        return (string) ($target[0]::query()->find($item->linked_source_id)?->slug ?? '') === $currentSlug;
    }
}
