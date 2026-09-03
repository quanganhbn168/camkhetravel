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
            ]))
            ->get()
            ->keyBy('id');
        View::share([
            'website' => $website,
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
            $requestPath = trim(request()->path(), '/');

            $applicationHost = parse_url((string) config('app.url'), PHP_URL_HOST);

            $headerNavigation = $headerMenu?->items
                ->whereNull('parent_id')
                ->map(fn (MenuItem $item): array => $this->menuItemData(
                    item: $item,
                    allItems: $headerMenu->items,
                    requestPath: $requestPath,
                    applicationHost: $applicationHost,
                ))
                ->values()
                ?? collect();

            $view->with([
                'headerLogoUrl' => MediaUrl::versioned($media->get($website->logo_media_id)),
                'headerNavigation' => $headerNavigation,
            ]);
        });
    }

    /** @return array{label: string, url: string, target: string, is_active: bool, home: bool, has_children: bool, children: Collection<int, array<string, mixed>>} */
    private function menuItemData(
        MenuItem $item,
        Collection $allItems,
        string $requestPath = '',
        ?string $applicationHost = null,
    ): array {
        $link = $item->link;
        $linkHost = parse_url($link, PHP_URL_HOST);
        $isInternalLink = $applicationHost !== null
            && $link !== '#'
            && ($linkHost === null || $linkHost === $applicationHost);
        $path = trim((string) parse_url($link, PHP_URL_PATH), '/');
        $isHome = false;
        $isActive = $isInternalLink
            && $path !== ''
            && ($requestPath === $path || str_starts_with($requestPath, $path.'/'));
        $children = $allItems
            ->where('parent_id', $item->getKey())
            ->map(fn (MenuItem $child): array => $this->menuItemData(
                item: $child,
                allItems: $allItems,
                requestPath: $requestPath,
                applicationHost: $applicationHost,
            ))
            ->values();

        return [
            'label' => $item->label,
            'url' => $link,
            'target' => $item->target ?: '_self',
            'is_active' => $isActive || $children->contains('is_active', true),
            'home' => $isHome,
            'has_children' => $children->isNotEmpty(),
            'children' => $children,
        ];
    }
}
