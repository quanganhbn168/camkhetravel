<?php

namespace App\Traits;

use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ảnh đại diện từ Curator (cột `curator_media_id`) và accessor `image_url` dùng chung cho frontend.
 * Khi đọc danh sách, luôn eager load `curatorMedia` để tránh N+1.
 */
trait HasCuratorImage
{
    public function curatorMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'curator_media_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return MediaUrl::resolve($this->curatorMedia);
    }
}
