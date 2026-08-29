<?php

namespace App\Support\Seo;

use App\Models\Landing;
use App\Models\Post;
use App\Models\Project;
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

}
