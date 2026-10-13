<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/** Cấu hình robots.txt và sitemap.xml. */
class CrawlSettings extends Settings
{
    /** Các nhóm nội dung có thể đưa vào sitemap. */
    public const SITEMAP_SECTIONS = [
        'pages' => 'Trang chính (trang chủ, danh sách, giới thiệu, liên hệ)',
        'services' => 'Dịch vụ',
        'solutions' => 'Giải pháp',
        'posts' => 'Bài viết',
        'products' => 'Sản phẩm',
        'product_categories' => 'Danh mục sản phẩm',
        'intros' => 'Bài giới thiệu',
        'site_pages' => 'Trang tự tạo',
    ];

    public bool $robots_block_all = false;

    public string $robots_disallow = '';

    public string $robots_extra = '';

    public array $sitemap_sections = [];

    public static function group(): string
    {
        return 'crawl';
    }

    /** @return list<string> Mỗi đường dẫn một dòng, luôn bắt đầu bằng "/". */
    public function disallowedPaths(): array
    {
        return collect(preg_split('/\R/u', $this->robots_disallow) ?: [])
            ->map(fn (string $line): string => trim($line))
            ->filter()
            ->map(fn (string $path): string => str_starts_with($path, '/') ? $path : '/'.$path)
            ->unique()
            ->values()
            ->all();
    }

    public function sitemapIncludes(string $section): bool
    {
        return in_array($section, $this->sitemap_sections, true);
    }
}
