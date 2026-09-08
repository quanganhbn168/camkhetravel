<?php

namespace App\Traits;

use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasSeoImage
{
    public function seoImageMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'seo_image_media_id');
    }

    public function seoImageUrl(?string $fallback = null): ?string
    {
        $media = $this->seoImageMedia;

        return $media && str_starts_with((string) $media->type, 'image/')
            ? (MediaUrl::versioned($media) ?: $fallback)
            : $fallback;
    }
}
