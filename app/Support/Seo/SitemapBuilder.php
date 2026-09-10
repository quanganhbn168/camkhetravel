<?php

namespace App\Support\Seo;

use App\Models\Intro;
use App\Models\LandingPage;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Support\Localization\LocalizedUrl;
use DateTimeInterface;

class SitemapBuilder
{
    /** @var array<string, array{url: string, last_modified: ?DateTimeInterface, frequency: string, priority: float}> */
    private array $urls = [];

    /** @var array<string, true> */
    private array $seenUrls = [];

    public function build(): self
    {
        $this->urls = [];
        $this->seenUrls = [];

        $this->addNativePages();
        $this->addNativeContent();

        foreach (Intro::query()->published()->with('slugs')->get() as $intro) {
            $this->addUrl($intro->url, $intro->updated_at, 'monthly', 0.6);
        }

        return $this;
    }

    public function render(): string
    {
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
        ];

        foreach ($this->urls as $entry) {
            $lines[] = '    <url>';
            $lines[] = '        <loc>'.$this->escape($entry['url']).'</loc>';

            if ($entry['last_modified'] instanceof DateTimeInterface) {
                $lines[] = '        <lastmod>'.$this->escape($entry['last_modified']->format(DateTimeInterface::ATOM)).'</lastmod>';
            }

            $lines[] = '        <changefreq>'.$this->escape($entry['frequency']).'</changefreq>';
            $lines[] = '        <priority>'.number_format($entry['priority'], 1, '.', '').'</priority>';
            $lines[] = '    </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines)."\n";
    }

    public function writeToFile(string $path): void
    {
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $this->render());
    }

    private function addNativePages(): void
    {
        foreach ([
            ['home', 'weekly', 1.0],
            ['services.index', 'weekly', 0.9],
            ['projects.index', 'weekly', 0.9],
            ['pricing.index', 'monthly', 0.7],
            ['posts.index', 'weekly', 0.8],
            ['about', 'monthly', 0.6],
            ['contact', 'monthly', 0.5],
        ] as [$route, $frequency, $priority]) {
            $this->addUrl(LocalizedUrl::route($route), null, $frequency, $priority);
        }
    }

    private function addNativeContent(): void
    {
        foreach ([
            [Service::class, 'monthly', 0.8],
            [LandingPage::class, 'monthly', 0.8],
            [Project::class, 'monthly', 0.8],
            [Post::class, 'monthly', 0.7],
        ] as [$model, $frequency, $priority]) {
            $model::query()
                ->published()
                ->with('slugs')
                ->get()
                ->each(function (Service|LandingPage|Project|Post $item) use ($frequency, $priority): void {
                    $url = match (true) {
                        $item instanceof Service => LocalizedUrl::service($item),
                        $item instanceof LandingPage => LocalizedUrl::landingPage($item),
                        $item instanceof Project => LocalizedUrl::project($item),
                        $item instanceof Post => LocalizedUrl::post($item),
                    };

                    if ($url !== '') {
                        $this->addUrl($url, $item->updated_at, $frequency, $priority);
                    }
                });
        }
    }

    private function addUrl(string $url, ?DateTimeInterface $lastModified, string $frequency, float $priority): void
    {
        $key = $this->normalizedUrlKey($url);

        if ($key === '' || isset($this->seenUrls[$key])) {
            return;
        }

        $this->seenUrls[$key] = true;
        $this->urls[$key] = [
            'url' => $url,
            'last_modified' => $lastModified,
            'frequency' => $frequency,
            'priority' => $priority,
        ];
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

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}
