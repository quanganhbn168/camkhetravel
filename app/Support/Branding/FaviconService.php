<?php

namespace App\Support\Branding;

use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;

final class FaviconService
{
    /**
     * @return array<int, array{rel: string, type: string, href: string, sizes?: string, color?: string}>
     */
    public function links(?Media $customMedia = null): array
    {
        $links = [
            [
                'rel' => 'icon',
                'type' => 'image/svg+xml',
                'href' => asset('favicon.svg'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '16x16',
                'href' => asset('favicon-16x16.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '32x32',
                'href' => asset('favicon-32x32.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '48x48',
                'href' => asset('favicon-48x48.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '96x96',
                'href' => asset('favicon-96x96.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/x-icon',
                'href' => asset('favicon.ico'),
            ],
            [
                'rel' => 'shortcut icon',
                'type' => 'image/x-icon',
                'href' => asset('favicon.ico'),
            ],
            [
                'rel' => 'apple-touch-icon',
                'type' => 'image/png',
                'sizes' => '180x180',
                'href' => asset('apple-touch-icon.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '192x192',
                'href' => asset('android-chrome-192x192.png'),
            ],
            [
                'rel' => 'icon',
                'type' => 'image/png',
                'sizes' => '512x512',
                'href' => asset('android-chrome-512x512.png'),
            ],
            [
                'rel' => 'mask-icon',
                'type' => 'image/svg+xml',
                'color' => '#ee6b2d',
                'href' => asset('favicon.svg'),
            ],
            [
                'rel' => 'manifest',
                'type' => 'application/manifest+json',
                'href' => asset('site.webmanifest'),
            ],
        ];

        $customUrl = MediaUrl::versioned($customMedia);

        if ($customUrl) {
            array_unshift($links, [
                'rel' => 'icon',
                'type' => MediaUrl::mimeType($customMedia),
                'sizes' => MediaUrl::iconSizes($customMedia),
                'href' => $customUrl,
            ]);
        }

        return $links;
    }

    public function primaryUrl(?Media $customMedia = null): string
    {
        return MediaUrl::versioned($customMedia) ?: asset('favicon.ico');
    }
}
