<?php

namespace App\Support\Localization;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectCategory;
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

        if (($parameters['landing'] ?? null) instanceof UrlRoutable) {
            $parameters['landing'] = $name === 'landings.comments.store'
                ? $parameters['landing']->getKey()
                : $parameters['landing']->getRouteKey();
        }

        if (($parameters['project'] ?? null) instanceof UrlRoutable) {
            $parameters['project'] = $name === 'projects.comments.store'
                ? $parameters['project']->getKey()
                : $parameters['project']->getRouteKey();
        }

        $languages = app(LanguageCatalog::class);
        $locale ??= app()->getLocale();
        $defaultLocale = $languages->defaultCode();
        $name = str_starts_with($name, 'localized.') ? substr($name, 10) : $name;

        if (! app('router')->has($locale === $defaultLocale ? $name : 'localized.'.$name)) {
            return self::fallbackUrl($name, $parameters, $locale);
        }

        if ($locale === $defaultLocale) {
            return self::absoluteRoute($name, $parameters);
        }

        return self::absoluteRoute('localized.'.$name, ['locale' => $locale, ...$parameters]);
    }

    public static function slug(string $slug, ?string $locale = null): string
    {
        return self::route('slug.show', ['slug' => $slug], $locale);
    }

    public static function post(Post $post, ?string $locale = null): string
    {
        return self::slug(self::contentSlug($post), $locale);
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

    public static function switchUrl(string $targetLocale): string
    {
        $languages = app(LanguageCatalog::class);
        abort_unless($languages->isSupported($targetLocale), 404);

        $route = request()->route();
        $name = $route?->getName();

        if (! $name) {
            return self::route('home', locale: $targetLocale);
        }

        $name = str_starts_with($name, 'localized.') ? substr($name, 10) : $name;
        $available = [
            'home', 'services.index', 'services.category', 'search', 'projects.index', 'projects.category', 'projects.show', 'pricing.index', 'about', 'bni.handover', 'bni.pickleball', 'bni.invitations.show', 'bni.articles.show', 'bni.member.login', 'contact', 'posts.index', 'posts.category', 'posts.show', 'slug.show',
        ];

        if (! in_array($name, $available, true)) {
            return self::route('home', locale: $targetLocale);
        }

        $parameters = $route->parameters();
        unset($parameters['locale']);

        return self::route($name, $parameters, $targetLocale);
    }

    private static function fallbackUrl(string $name, array $parameters, string $locale): string
    {
        $paths = [
            'home' => '',
            'services.index' => 'dich-vu',
            'services.category' => 'dich-vu/danh-muc/'.($parameters['category'] ?? ''),
            'search' => 'tim-kiem',
            'projects.index' => 'du-an',
            'projects.category' => 'du-an/danh-muc/'.($parameters['slug'] ?? ''),
            'projects.show' => 'du-an/'.($parameters['slug'] ?? ''),
            'pricing.index' => 'bang-gia',
            'about' => 'gioi-thieu',
            'bni.handover' => 'le-chuyen-giao',
            'bni.pickleball' => 'le-chuyen-giao/pickleball',
            'bni.invitations.show' => 'le-chuyen-giao/thu-moi/'.($parameters['invitation'] ?? ''),
            'bni.articles.show' => 'le-chuyen-giao/tin-tuc/'.($parameters['article'] ?? ''),
            'bni.member.login' => 'le-chuyen-giao/dang-nhap',
            'contact' => 'lien-he',
            'contact.store' => 'lien-he',
            'comments.store' => 'binh-luan/'.($parameters['post'] ?? ''),
            'landings.comments.store' => 'binh-luan/dich-vu/'.($parameters['landing'] ?? ''),
            'projects.comments.store' => 'binh-luan/du-an/'.($parameters['project'] ?? ''),
            'posts.index' => 'tin-tuc',
            'posts.category' => 'tin-tuc/danh-muc/'.($parameters['slug'] ?? ''),
            'posts.show' => 'tin-tuc/'.($parameters['slug'] ?? ''),
            'slug.show' => $parameters['slug'] ?? '',
        ];
        $path = trim($paths[$name] ?? '', '/');
        $prefix = $locale === app(LanguageCatalog::class)->defaultCode() ? '' : $locale;
        $segments = array_filter([$prefix, $path]);

        return rtrim((string) config('app.url'), '/').'/'.implode('/', $segments);
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

    private static function contentSlug(Post|Project $content): string
    {
        return $content->legacyContent?->slug
            ?: $content->slug
            ?: Str::slug($content->title);
    }

    private static function termSlug(PostCategory|ProjectCategory $category): string
    {
        return $category->legacyTerm?->slug
            ?: $category->slug
            ?: Str::slug($category->name);
    }
}
