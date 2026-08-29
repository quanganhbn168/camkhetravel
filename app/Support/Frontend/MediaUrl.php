<?php

namespace App\Support\Frontend;

use Awcodes\Curator\Models\Media;

class MediaUrl
{
    public static function resolve(?Media $media): ?string
    {
        return $media?->url;
    }
}
