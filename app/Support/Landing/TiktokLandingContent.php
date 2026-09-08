<?php

namespace App\Support\Landing;

use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;

final class TiktokLandingContent
{
    public static function resolveMedia(array $content): array
    {
        $ids = [];
        array_walk_recursive($content, function ($value, $key) use (&$ids): void {
            if ($key === 'image_media_id' && is_numeric($value)) {
                $ids[] = (int) $value;
            }
        });
        if ($ids === []) {
            return $content;
        }
        $media = Media::query()->whereIn('id', array_unique($ids))->get()->keyBy('id');
        $resolve = function (array $node) use (&$resolve, $media): array {
            foreach ($node as $key => $value) {
                if (is_array($value)) {
                    $node[$key] = $resolve($value);
                }
            }
            if (is_numeric($node['image_media_id'] ?? null)) {
                $node['image'] = MediaUrl::versioned($media->get((int) $node['image_media_id'])) ?: ($node['image'] ?? '');
            }

            return $node;
        };

        return $resolve($content);
    }
}
