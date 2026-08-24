<?php

namespace App\Support\Frontend;

use App\Models\MediaAsset;
use Awcodes\Curator\Models\Media;

class MediaUrl
{
    public static function resolve(?Media $curatorMedia, ?MediaAsset $legacyMedia): ?string
    {
        return $curatorMedia?->url ?: $legacyMedia?->public_url;
    }
}
