<?php

namespace App\Providers;

use App\Models\ContentItem;
use App\Models\HeroSlide;
use App\Models\Language;
use App\Models\Landing;
use App\Models\LandingCategory;
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
use App\Support\Localization\LanguageCatalog;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
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
            'landing' => Landing::class,
            'landing-category' => LandingCategory::class,
            'legacy-service' => Service::class,
            'legacy-service-category' => ServiceCategory::class,
            'user' => User::class,
        ]);

        Post::observe(SlugObserver::class);
        PostCategory::observe(SlugObserver::class);
        Project::observe(SlugObserver::class);
        ProjectCategory::observe(SlugObserver::class);
        Landing::observe(SlugObserver::class);
        LandingCategory::observe(SlugObserver::class);

        ContentItem::observe(AssignNextOrderObserver::class);
        HeroSlide::observe(AssignNextOrderObserver::class);
        Language::observe(AssignNextOrderObserver::class);
        Partner::observe(AssignNextOrderObserver::class);
        PostCategory::observe(AssignNextOrderObserver::class);
        PricingPlan::observe(AssignNextOrderObserver::class);
        Project::observe(AssignNextOrderObserver::class);
        ProjectCategory::observe(AssignNextOrderObserver::class);
        Landing::observe(AssignNextOrderObserver::class);
        LandingCategory::observe(AssignNextOrderObserver::class);
        Testimonial::observe(AssignNextOrderObserver::class);

        ContentItem::observe(ContentSeoFallbackObserver::class);
        Post::observe(ContentSeoFallbackObserver::class);
        Project::observe(ContentSeoFallbackObserver::class);
        Landing::observe(ContentSeoFallbackObserver::class);

        RateLimiter::for('frontend-contact', fn ($request) => Limit::perMinute(5)->by((string) $request->ip()));
        RateLimiter::for('frontend-comment', fn ($request) => Limit::perMinute(3)->by((string) $request->ip()));

        $website = app(WebsiteSettings::class);
        $media = Media::query()
            ->whereIn('id', array_filter([
                $website->logo_media_id,
                $website->favicon_media_id,
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
            'seo' => app(FrontendSeoBuilder::class)->default(),
            'languages' => app(LanguageCatalog::class)->active(),
            'indexableLanguages' => app(LanguageCatalog::class)->indexable(),
        ]);

        View::composer('partials.footer', function (BladeView $view): void {
            $view->with('footerServices', Landing::query()
                ->published()
                ->with('slugs')
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->limit(4)
                ->get(['id', 'title']));
        });

        View::composer('partials.header', function (BladeView $view): void {
            $megaServiceCategories = LandingCategory::query()
                ->where('is_active', true)
                ->whereHas('landings', fn (Builder $query) => $query->published())
                ->with([
                    'landings' => fn (HasMany $query) => $query
                        ->published()
                        ->with('slugs')
                        ->orderByDesc('is_featured')
                        ->orderBy('sort_order')
                        ->orderBy('title'),
                ])
                ->orderBy('sort_order')
                ->get()
                ->each(fn (LandingCategory $category) => $category->setRelation(
                    'landings',
                    $category->landings->take(12)->values(),
                ));

            $view->with('megaServiceCategories', $megaServiceCategories);
        });
    }
}
