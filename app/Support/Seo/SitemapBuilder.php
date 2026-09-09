<?php

namespace App\Support\Seo;

use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\Intro;
use App\Models\LandingPage;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Support\Events\EventCatalog;
use App\Support\Localization\LocalizedUrl;
use DateTimeInterface;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapBuilder
{
    /** @var array<string, true> */
    private array $seenUrls = [];

    public function build(): Sitemap
    {
        $this->seenUrls = [];
        $sitemap = Sitemap::create();

        $this->addNativePages($sitemap);
        $this->addNativeContent($sitemap);
        $this->addBniChapters($sitemap);
        $catalog = app(EventCatalog::class);
        BniEvent::query()->published()->get()->each(fn (BniEvent $event) => $this->addUrl(
            $sitemap, $catalog->url($event), $event->updated_at, Url::CHANGE_FREQUENCY_MONTHLY, 0.6,
        ));

        foreach (Intro::query()->published()->get() as $intro) {
            $sitemap->add(Url::create($intro->url)->setLastModificationDate($intro->updated_at)->setPriority(0.6));
        }

        return $sitemap;
    }

    private function addNativePages(Sitemap $sitemap): void
    {
        foreach ([
            ['home', Url::CHANGE_FREQUENCY_WEEKLY, 1.0],
            ['services.index', Url::CHANGE_FREQUENCY_WEEKLY, 0.9],
            ['projects.index', Url::CHANGE_FREQUENCY_WEEKLY, 0.9],
            ['pricing.index', Url::CHANGE_FREQUENCY_MONTHLY, 0.7],
            ['posts.index', Url::CHANGE_FREQUENCY_WEEKLY, 0.8],
            ['bni.events.index', Url::CHANGE_FREQUENCY_WEEKLY, 0.8],
            ['about', Url::CHANGE_FREQUENCY_MONTHLY, 0.6],
            ['bni.handover', Url::CHANGE_FREQUENCY_MONTHLY, 0.5],
            ['bni.articles.index', Url::CHANGE_FREQUENCY_WEEKLY, 0.6],
            ['bni.gallery.index', Url::CHANGE_FREQUENCY_WEEKLY, 0.5],
            ['bni.pickleball', Url::CHANGE_FREQUENCY_WEEKLY, 0.5],
            ['contact', Url::CHANGE_FREQUENCY_MONTHLY, 0.5],
        ] as [$route, $frequency, $priority]) {
            $this->addUrl($sitemap, LocalizedUrl::route($route), null, $frequency, $priority);
        }
    }

    private function addNativeContent(Sitemap $sitemap): void
    {
        foreach ([
            [Service::class, Url::CHANGE_FREQUENCY_MONTHLY, 0.8],
            [LandingPage::class, Url::CHANGE_FREQUENCY_MONTHLY, 0.8],
            [Project::class, Url::CHANGE_FREQUENCY_MONTHLY, 0.8],
            [Post::class, Url::CHANGE_FREQUENCY_MONTHLY, 0.7],
        ] as [$model, $frequency, $priority]) {
            $model::query()
                ->published()
                ->with('slugs')
                ->get()
                ->each(function (Service|LandingPage|Project|Post $item) use ($sitemap, $frequency, $priority): void {
                    $url = match (true) {
                        $item instanceof Service => LocalizedUrl::service($item),
                        $item instanceof LandingPage => LocalizedUrl::landingPage($item),
                        $item instanceof Project => LocalizedUrl::project($item),
                        $item instanceof Post => LocalizedUrl::post($item),
                    };

                    if ($url) {
                        $this->addUrl($sitemap, $url, $item->updated_at, $frequency, $priority);
                    }
                });
        }
    }

    private function addBniChapters(Sitemap $sitemap): void
    {
        BniChapter::query()
            ->where('is_active', true)
            ->where(fn ($query) => $query
                ->whereNull('bni_event_id')
                ->orWhereHas('event', fn ($query) => $query->published()))
            ->get()
            ->each(fn (BniChapter $chapter) => $this->addUrl(
                $sitemap,
                LocalizedUrl::route('bni.chapters.show', ['chapter' => $chapter->slug]),
                $chapter->updated_at,
                Url::CHANGE_FREQUENCY_MONTHLY,
                0.6,
            ));
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
