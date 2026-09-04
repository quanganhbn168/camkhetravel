<?php

namespace App\Support\Landing;

use RuntimeException;

/**
 * Native data adapter for the source WordPress communication landing page.
 *
 * The JSON snapshot is generated from the old THT Media checkout and kept as
 * page data, while the public renderer remains entirely Laravel-owned.
 */
final class CommunicationsLandingContent
{
    /** @return array<string, mixed> */
    public static function source(): array
    {
        static $source;

        if (is_array($source)) {
            return $source;
        }

        $path = resource_path('data/landing-07-communications-source.json');
        $contents = is_file($path) ? file_get_contents($path) : false;

        if (! is_string($contents) || trim($contents) === '') {
            throw new RuntimeException('Communications landing source data is missing.');
        }

        $source = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);

        return $source;
    }

    /** @return list<array<string, mixed>> */
    public static function sections(): array
    {
        $source = self::source();

        return [
            ['type' => 'hero', 'data' => ['block_id' => 'hero']],
            ['type' => 'content_grid', 'data' => ['block_id' => 'giai-phap']],
            ['type' => 'process', 'data' => ['block_id' => 'quy-trinh']],
            ['type' => 'projects', 'data' => ['block_id' => 'du-an', 'limit' => 9]],
            ['type' => 'pricing', 'data' => ['block_id' => 'bang-gia']],
            ['type' => 'faqs', 'data' => ['block_id' => 'khach-hang']],
            ['type' => 'lead_form', 'data' => ['block_id' => 'lien-he']],
        ];
    }

    /** @return array<string, mixed> */
    public static function templateSettings(): array
    {
        $source = self::source();

        return [
            'landing07_brand_label' => 'THT MEDIA',
            'landing07_service_line' => 'Chiến lược · Media · Quảng cáo · Case study',
            'landing07_nav_cta' => 'Nhận tư vấn miễn phí',
            'landing07_footer_text' => 'THT Media · Đồng hành từ chiến lược đến triển khai',
            'communications_source' => $source,
        ];
    }
}
