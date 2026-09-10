<?php

namespace App\Support\Localization;

use App\Models\LandingPage;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Support\Str;

class LocalizedUrl
{
    public static function route(string $name, array $parameters = [], ?string $locale = null): string
    {
        if (($parameters['post'] ?? null) instanceof UrlRoutable) {
            $parameters['post'] = $name === 'comments.store'
                ? $parameters['post']->getKey()
                : $parameters['post']->getRouteKey();
        }

        if (($parameters['service'] ?? null) instanceof UrlRoutable) {
            $parameters['service'] = $name === 'services.comments.store'
                ? $parameters['service']->getKey()
                : $parameters['service']->getRouteKey();
        }

        if (($parameters['landingPage'] ?? null) instanceof UrlRoutable) {
            $parameters['landingPage'] = $name === 'landing-pages.comments.store'
                ? $parameters['landingPage']->getKey()
                : $parameters['landingPage']->getRouteKey();
        }

        if (($parameters['project'] ?? null) instanceof UrlRoutable) {
            $parameters['project'] = $name === 'projects.comments.store'
                ? $parameters['project']->getKey()
                : $parameters['project']->getRouteKey();
        }

        if (! app('router')->has($name)) {
            return self::fallbackUrl($name, $parameters);
        }

        return self::absoluteRoute($name, $parameters);
    }

    public static function slug(string $slug, ?string $locale = null): string
    {
        return self::route('slug.show', ['slug' => $slug], $locale);
    }

    public static function post(Post $post, ?string $locale = null): string
    {
        return self::slug(self::contentSlug($post), $locale);
    }

    public static function service(Service $service, ?string $locale = null): string
    {
        return self::slug(self::contentSlug($service), $locale);
    }

    public static function product(Product $product, ?string $locale = null): string
    {
        return self::route('products.show', ['slug' => self::contentSlug($product)], $locale);
    }

    public static function productCategory(ProductCategory $category, ?string $locale = null): string
    {
        return self::route('products.category', ['slug' => self::termSlug($category)], $locale);
    }

    public static function serviceCategory(ServiceCategory $category, ?string $locale = null): string
    {
        return self::route('services.category', ['category' => self::termSlug($category)], $locale);
    }

    public static function landingPage(LandingPage $landingPage, ?string $locale = null): string
    {
        return self::slug(self::contentSlug($landingPage), $locale);
    }

    public static function postCategory(PostCategory $category, ?string $locale = null): string
    {
        return self::route('posts.category', ['slug' => self::termSlug($category)], $locale);
    }

    public static function project(Project $project, ?string $locale = null): string
    {
        return self::route('projects.show', ['slug' => self::contentSlug($project)], $locale);
    }

    public static function projectCategory(ProjectCategory $category, ?string $locale = null): string
    {
        return self::route('projects.category', ['slug' => self::termSlug($category)], $locale);
    }

    private static function fallbackUrl(string $name, array $parameters): string
    {
        $paths = [
            'home' => '',
            'services.index' => 'dich-vu',
            'services.category' => 'dich-vu/'.($parameters['category'] ?? ''),
            'search' => 'tim-kiem',
            'projects.index' => 'du-an',
            'projects.category' => 'du-an/danh-muc/'.($parameters['slug'] ?? ''),
            'projects.show' => 'du-an/'.($parameters['slug'] ?? ''),
            'pricing.index' => 'bang-gia',
            'about' => 'gioi-thieu',
            'contact' => 'lien-he',
            'contact.store' => 'lien-he',
            'comments.store' => 'binh-luan/'.($parameters['post'] ?? ''),
            'services.comments.store' => 'binh-luan/dich-vu/'.($parameters['service'] ?? ''),
            'products.index' => 'san-pham',
            'products.category' => 'san-pham/danh-muc/'.($parameters['slug'] ?? ''),
            'products.show' => 'san-pham/'.($parameters['slug'] ?? ''),
            'landing-pages.comments.store' => 'binh-luan/landing-page/'.($parameters['landingPage'] ?? ''),
            'projects.comments.store' => 'binh-luan/du-an/'.($parameters['project'] ?? ''),
            'posts.index' => 'blog',
            'posts.category' => 'blog/danh-muc/'.($parameters['slug'] ?? ''),
            'posts.show' => 'blog/'.($parameters['slug'] ?? ''),
            'slug.show' => $parameters['slug'] ?? '',
        ];
        $path = trim($paths[$name] ?? '', '/');

        return rtrim((string) config('app.url'), '/').'/'.$path;
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private static function absoluteRoute(string $name, array $parameters): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');
        $path = route($name, $parameters, false);

        return $path === '/'
            ? $baseUrl.'/'
            : $baseUrl.'/'.ltrim($path, '/');
    }

    private static function contentSlug(Post|Project|Service|LandingPage|Product $content): string
    {
        return $content->slug ?: Str::slug($content->title);
    }

    private static function termSlug(PostCategory|ProjectCategory|ServiceCategory|ProductCategory $category): string
    {
        return $category->slug ?: Str::slug($category->name);
    }
}
