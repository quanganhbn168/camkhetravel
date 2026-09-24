<?php

namespace App\Providers;

use App\Models\HeroSlide;
use App\Models\Intro;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Partner;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Solution;
use App\Models\SolutionCategory;
use App\Models\Testimonial;
use App\Models\User;
use App\Observers\AssignNextOrderObserver;
use App\Observers\ContentSeoFallbackObserver;
use App\Observers\SlugObserver;
use App\Settings\WebsiteSettings;
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
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'intro' => Intro::class,
            'post' => Post::class,
            'post-category' => PostCategory::class,
            'product' => Product::class,
            'product-category' => ProductCategory::class,
            'service' => Service::class,
            'solution' => Solution::class,
            'solution-category' => SolutionCategory::class,
            'service-category' => ServiceCategory::class,
            'user' => User::class,
        ]);

        Solution::observe(SlugObserver::class);
        SolutionCategory::observe(SlugObserver::class);

        foreach ([
            Post::class,
            PostCategory::class,
            Product::class,
            ProductCategory::class,
            Intro::class,
            Service::class,
            ServiceCategory::class,
        ] as $model) {
            $model::observe(SlugObserver::class);
        }

        foreach ([
            HeroSlide::class,
            Partner::class,
            PostCategory::class,
            Product::class,
            ProductCategory::class,
            Service::class,
            ServiceCategory::class,
            Testimonial::class,
        ] as $model) {
            $model::observe(AssignNextOrderObserver::class);
        }

        foreach ([
            Post::class,
            PostCategory::class,
            Product::class,
            ProductCategory::class,
            Service::class,
            ServiceCategory::class,
            Intro::class,
        ] as $model) {
            $model::observe(ContentSeoFallbackObserver::class);
        }

        RateLimiter::for('frontend-contact', fn ($request) => Limit::perMinute(5)->by((string) $request->ip()));
        RateLimiter::for('frontend-comment', fn ($request) => Limit::perMinute(3)->by((string) $request->ip()));

        // Console commands must be able to boot before the website database exists.
        if ($this->app->runningInConsole() && ! filter_var(env('APP_TESTING_HTTP', false), FILTER_VALIDATE_BOOL)) {
            return;
        }

        $website = app(WebsiteSettings::class);
        $mediaIds = array_values(array_filter([
            $website->logo_media_id,
            $website->seo_image_media_id,
            $website->company_profile_media_id,
            $website->about_image_media_id,
            $website->banner_media_id,
            $website->footer_background_media_id,
        ]));

        $media = $mediaIds === []
            ? collect()
            : Media::query()->whereIn('id', $mediaIds)->get()->keyBy('id');

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
            'seo' => app(FrontendSeoBuilder::class)->default(),
            'footerContactPhones' => $this->phoneLinks($website),
            'footerContactBranches' => $this->contactBranches($website),
            'currentYear' => now()->year,
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

            $footerServices = request()->attributes->get('frontend.footer_services');

            if (! $footerServices instanceof Collection) {
                $footerServices = Service::query()
                    ->published()
                    ->with('slugs')
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')
                    ->limit(4)
                    ->get(['id', 'title', 'is_featured', 'sort_order']);
            }

            $view->with('footerServices', $footerServices)
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
                ->map(fn (MenuItem $item): array => $this->menuItemData($item, $headerMenu->items))
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
        return $this->phoneLinks($website)->take(2)->values();
    }

    /** @return Collection<int, array{label: string, href: string}> */
    private function phoneLinks(WebsiteSettings $website): Collection
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
        ])->unique('href')->values();
    }

    /** @return Collection<int, array{name: string, address: string}> */
    private function contactBranches(WebsiteSettings $website): Collection
    {
        return collect($website->branches ?? [])
            ->filter(fn ($branch): bool => is_array($branch)
                && ($branch['is_active'] ?? true)
                && filled($branch['address'] ?? null))
            ->map(fn (array $branch): array => [
                'name' => (string) ($branch['name'] ?? 'Địa chỉ'),
                'address' => trim((string) $branch['address']),
            ])
            ->values();
    }

    /** @return array{label: string, url: string, target: string, is_active: bool, home: bool, has_children: bool, children: Collection<int, array<string, mixed>>} */
    private function menuItemData(MenuItem $item, Collection $allItems): array
    {
        $link = $item->link;
        $isActive = $this->menuItemMatchesCurrentRoute($item, $link);
        $children = $allItems
            ->where('parent_id', $item->getKey())
            ->map(fn (MenuItem $child): array => $this->menuItemData($child, $allItems))
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

    private function menuItemMatchesCurrentRoute(MenuItem $item, string $link): bool
    {
        if ($link !== '#' && ! str_starts_with($link, '#')) {
            $parts = parse_url($link);

            if ($parts !== false
                && ! isset($parts['fragment'])
                && (! isset($parts['host']) || strcasecmp($parts['host'], request()->getHost()) === 0)) {
                $path = '/'.trim((string) ($parts['path'] ?? ''), '/');
                $currentPath = '/'.trim(request()->path(), '/');

                if ($path === $currentPath) {
                    return true;
                }

                if ($path === '/dich-vu' && (request()->routeIs('services.*')
                    || request()->attributes->get('frontend.content_type') === 'service')) {
                    return true;
                }

                if ($path === '/blog' && (request()->routeIs('posts.*')
                    || request()->attributes->get('frontend.content_type') === 'post')) {
                    return true;
                }

                if ($path === '/giai-phap' && request()->routeIs('solutions.*')) {
                    return true;
                }

                if ($path === '/san-pham' && request()->routeIs('products.*')) {
                    return true;
                }
            }
        }

        $linkedSourceType = (string) $item->linked_source_type;

        if ($linkedSourceType === 'native_route') {
            $routeName = trim((string) $item->getRawOriginal('url'));

            if ($routeName === 'solutions.index') {
                return request()->routeIs('solutions.*');
            }

            return $routeName !== '' && request()->routeIs($routeName);
        }

        $target = match ($linkedSourceType) {
            'native_service', Service::class, 'service' => [Service::class, 'slug.show'],
            'native_service_category', ServiceCategory::class, 'service-category' => [ServiceCategory::class, 'services.category', 'category'],
            'native_post', Post::class, 'post' => [Post::class, 'posts.show'],
            'native_post_category', PostCategory::class, 'post-category' => [PostCategory::class, 'posts.category'],
            'native_product', Product::class, 'product' => [Product::class, 'products.show'],
            'native_product_category', ProductCategory::class, 'product-category' => [ProductCategory::class, 'products.category'],
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

        return $currentSlug !== ''
            && (string) ($target[0]::query()->find($item->linked_source_id)?->slug ?? '') === $currentSlug;
    }
}
