<?php

namespace App\Support\Seo;

use App\Settings\CrawlSettings;

final class RobotsTxt
{
    public function __construct(private readonly CrawlSettings $crawl) {}

    public function render(): string
    {
        $baseUrl = rtrim((string) config('app.url'), '/');

        if ($this->crawl->robots_block_all) {
            return implode(PHP_EOL, ['User-agent: *', 'Disallow: /', '']);
        }

        $lines = ['User-agent: *', 'Allow: /'];

        foreach ($this->crawl->disallowedPaths() as $path) {
            $lines[] = 'Disallow: '.$path;
        }

        $extra = trim(str_replace("\r\n", "\n", $this->crawl->robots_extra));

        if ($extra !== '') {
            array_push($lines, '', ...explode("\n", $extra));
        }

        array_push($lines, '', 'Sitemap: '.$baseUrl.'/sitemap.xml', '');

        return implode(PHP_EOL, $lines);
    }
}
