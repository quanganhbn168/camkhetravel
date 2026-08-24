<?php

namespace App\Support\Seo;

class ArchiveDefinition
{
    private const DEFINITIONS = [
        'blog' => ['category', 'blog', 'post', '/blog/'],
        'danh-muc-dich-vu/dich-vu-va-bang-gia' => ['danh-muc-dich-vu', 'dich-vu-va-bang-gia', 'service', '/danh-muc-dich-vu/dich-vu-va-bang-gia/'],
        'danh-muc-du-an/anh' => ['us_portfolio_category', 'anh', 'us_portfolio', '/danh-muc-du-an/anh/'],
        'danh-muc-du-an/phong-su-cuoi' => ['us_portfolio_category', 'phong-su-cuoi', 'us_portfolio', '/danh-muc-du-an/phong-su-cuoi/'],
        'danh-muc-du-an/tvc-cua-hang' => ['us_portfolio_category', 'tvc-cua-hang', 'us_portfolio', '/danh-muc-du-an/tvc-cua-hang/'],
        'danh-muc-du-an/tvc-doanh-nghiep' => ['us_portfolio_category', 'tvc-doanh-nghiep', 'us_portfolio', '/danh-muc-du-an/tvc-doanh-nghiep/'],
        'danh-muc-du-an/video-highlight' => ['us_portfolio_category', 'video-highlight', 'us_portfolio', '/danh-muc-du-an/video-highlight/'],
        'danh-muc-du-an/video' => ['us_portfolio_category', 'video', 'us_portfolio', '/danh-muc-du-an/video/'],
        'landing-cate/dao-tao' => ['landing-cate', 'dao-tao', 'landing', '/landing-cate/dao-tao/'],
        'landing-cate/dich-vu-media' => ['landing-cate', 'dich-vu-media', 'landing', '/landing-cate/dich-vu-media/'],
        'landing-cate/truyen-thong-quang-cao' => ['landing-cate', 'truyen-thong-quang-cao', 'landing', '/landing-cate/truyen-thong-quang-cao/'],
    ];

    public static function find(string $path): ?array
    {
        $key = trim($path, '/');

        if (preg_match('#^(blog)/page/([1-9][0-9]*)$#', $key, $matches)) {
            $definition = self::DEFINITIONS[$matches[1]];
            $definition['page'] = (int) $matches[2];

            return self::named($definition);
        }

        return isset(self::DEFINITIONS[$key])
            ? self::named(self::DEFINITIONS[$key])
            : null;
    }

    public static function sitemapPaths(): array
    {
        return collect(self::DEFINITIONS)
            ->reject(fn (array $definition) => in_array($definition[1], ['blog', 'video-highlight'], true))
            ->pluck(3)
            ->values()
            ->all();
    }

    private static function named(array $definition): array
    {
        return [
            'taxonomy' => $definition[0],
            'term' => $definition[1],
            'content_type' => $definition[2],
            'path' => $definition[3],
            'page' => $definition['page'] ?? 1,
        ];
    }
}
