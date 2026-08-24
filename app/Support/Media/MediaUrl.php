<?php

namespace App\Support\Media;

use Awcodes\Curator\Models\Media;

class MediaUrl
{
    public static function versioned(?Media $media): ?string
    {
        if (! $media?->url) {
            return null;
        }

        $separator = str_contains($media->url, '?') ? '&' : '?';
        $version = $media->updated_at?->getTimestamp() ?? $media->getKey();

        return "{$media->url}{$separator}v={$version}";
    }

    public static function mimeType(?Media $media): string
    {
        return match (strtolower((string) $media?->ext)) {
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };
    }

    public static function iconSizes(?Media $media): string
    {
        if (! $media?->width || ! $media->height) {
            return 'any';
        }

        return "{$media->width}x{$media->height}";
    }
}
