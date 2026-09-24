<?php

namespace App\Support\Seo;

use App\Models\Intro;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
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
        foreach (\App\Models\Solution::published()->with('slugs')->get() as $solution) {
            $this->addUrl(route('solutions.show', ['solution' => $solution->slug]), $solution->updated_at, 'monthly', 0.7);
        }

        ProductCategory::query()
            ->active()
            ->with('slugs')
            ->get()
            ->each(function (ProductCategory $category): void {
                $this->addUrl(
                    route('products.category', ['slug' => $category->slug]),
                    $category->updated_at,
                    'monthly',
                    0.6,
                );
            });

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
            ['solutions.index', 'weekly', 0.9],
            ['products.index', 'weekly', 0.8],
            ['posts.index', 'weekly', 0.8],
            ['about', 'monthly', 0.6],
            ['contact', 'monthly', 0.5],
        ] as [$route, $frequency, $priority]) {
            $this->addUrl(route($route), null, $frequency, $priority);
        }
    }

    private function addNativeContent(): void
    {
        foreach ([
            [Service::class, 'monthly', 0.8],
            [Post::class, 'monthly', 0.7],
            [Product::class, 'monthly', 0.8],
        ] as [$model, $frequency, $priority]) {
            $model::query()
                ->published()
                ->with('slugs')
                ->get()
                ->each(function (Service|Post|Product $item) use ($frequency, $priority): void {
                    $url = match (true) {
                        $item instanceof Service => route('slug.show', ['slug' => $item->slug]),
                        $item instanceof Post => route('slug.show', ['slug' => $item->slug]),
                        $item instanceof Product => route('products.show', ['slug' => $item->slug]),
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
