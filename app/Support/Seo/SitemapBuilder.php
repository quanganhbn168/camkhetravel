<?php

namespace App\Support\Seo;

use App\Models\ContentItem;
use App\Models\Landing;
use App\Models\Post;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Support\Localization\LanguageCatalog;
use App\Support\Localization\LocalizedUrl;
use DateTimeInterface;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapBuilder
{
    /** @var array<string, true> */
    private array $seenUrls = [];

    public function __construct(private readonly LanguageCatalog $languages) {}

    public function build(): Sitemap
    {
        $this->seenUrls = [];
        $sitemap = Sitemap::create();

        $this->addNativePages($sitemap);
        $this->addNativeContent($sitemap);
        $this->addImportedContent($sitemap);

        return $sitemap;
    }

    private function addNativePages(Sitemap $sitemap): void
    {
        $defaultLocale = $this->languages->defaultCode();

        foreach ([
            ['home', Url::CHANGE_FREQUENCY_WEEKLY, 1.0],
            ['services.index', Url::CHANGE_FREQUENCY_WEEKLY, 0.9],
            ['projects.index', Url::CHANGE_FREQUENCY_WEEKLY, 0.9],
            ['pricing.index', Url::CHANGE_FREQUENCY_MONTHLY, 0.7],
            ['posts.index', Url::CHANGE_FREQUENCY_WEEKLY, 0.8],
            ['about', Url::CHANGE_FREQUENCY_MONTHLY, 0.6],
            ['bni.handover', Url::CHANGE_FREQUENCY_MONTHLY, 0.5],
            ['bni.pickleball', Url::CHANGE_FREQUENCY_WEEKLY, 0.5],
            ['contact', Url::CHANGE_FREQUENCY_MONTHLY, 0.5],
        ] as [$route, $frequency, $priority]) {
            $this->addUrl($sitemap, LocalizedUrl::route($route, locale: $defaultLocale), null, $frequency, $priority);
        }
    }

    private function addNativeContent(Sitemap $sitemap): void
    {
        foreach ([
            [Landing::class, Url::CHANGE_FREQUENCY_MONTHLY, 0.8],
            [Project::class, Url::CHANGE_FREQUENCY_MONTHLY, 0.8],
            [Post::class, Url::CHANGE_FREQUENCY_MONTHLY, 0.7],
        ] as [$model, $frequency, $priority]) {
            $model::query()
                ->published()
                ->with('slugs')
                ->get()
                ->each(function (Landing|Project|Post $item) use ($sitemap, $frequency, $priority): void {
                    $url = match (true) {
                        $item instanceof Landing => filled($item->slug) ? LocalizedUrl::slug($item->slug) : null,
                        $item instanceof Project => LocalizedUrl::project($item),
                        $item instanceof Post => LocalizedUrl::post($item),
                    };

                    if ($url) {
                        $this->addUrl($sitemap, $url, $item->updated_at, $frequency, $priority);
                    }
                });
        }
    }

    private function addImportedContent(Sitemap $sitemap): void
    {
        $frontPageId = (int) SiteSetting::query()
            ->where('group', 'wordpress')
            ->where('key', 'page_on_front')
            ->value('value');

        ContentItem::query()
            ->where('source', 'wordpress')
            ->where('status', 'published')
            ->whereNotNull('canonical_path')
            ->whereNotIn('type', ['landing', 'service'])
            ->whereNotIn('id', Landing::query()->whereNotNull('legacy_content_item_id')->select('legacy_content_item_id'))
            ->whereNotIn('id', Project::query()->whereNotNull('legacy_content_item_id')->select('legacy_content_item_id'))
            ->whereNotIn('id', Post::query()->whereNotNull('legacy_content_item_id')->select('legacy_content_item_id'))
            ->where('exclude_from_sitemap', false)
            ->whereNotIn('canonical_path', [
                '/404-not-found/',
                '/search/',
                '/under-construction/',
                '/test/',
            ])
            ->orderByDesc('content_modified_at')
            ->get(['source_id', 'canonical_path', 'content_modified_at'])
            ->each(function (ContentItem $item) use ($sitemap, $frontPageId): void {
                $path = $frontPageId > 0 && (int) $item->source_id === $frontPageId
                    ? '/'
                    : $item->canonical_path;

                $this->addUrl($sitemap, $this->absolutePath($path), $item->content_modified_at, Url::CHANGE_FREQUENCY_MONTHLY, 0.5);
            });

        foreach (ArchiveDefinition::sitemapPaths() as $path) {
            $this->addUrl($sitemap, $this->absolutePath($path), null, Url::CHANGE_FREQUENCY_MONTHLY, 0.4);
        }
    }

    private function addUrl(Sitemap $sitemap, string $url, ?DateTimeInterface $lastModified, string $frequency, float $priority): void
    {
        $key = $this->normalizedUrlKey($url);

        if (isset($this->seenUrls[$key])) {
            return;
        }

        $this->seenUrls[$key] = true;

        $tag = Url::create($url)
            ->setChangeFrequency($frequency)
            ->setPriority($priority);

        if ($lastModified instanceof DateTimeInterface) {
            $tag->setLastModificationDate($lastModified);
        }

        $sitemap->add($tag);
    }

    private function normalizedUrlKey(string $url): string
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['host'])) {
            return rtrim($url, '/');
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? 'https'));
        $host = strtolower($parts['host']);
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = (string) ($parts['path'] ?? '/');
        $path = $path === '/' ? '' : rtrim($path, '/');
        $query = isset($parts['query']) ? '?'.$parts['query'] : '';

        return $scheme.'://'.$host.$port.$path.$query;
    }

    private function absolutePath(string $path): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');

        return $path === '/'
            ? $baseUrl.'/'
            : $baseUrl.'/'.ltrim($path, '/');
    }
}
