<?php

namespace App\Models;

use App\Support\Media\MediaUrl;
use App\Traits\HasSeoImage;
use App\Traits\HasSlug;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Solution extends Model
{
    use HasSeoImage, HasSlug;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'is_home' => 'boolean', 'sort_order' => 'integer', 'highlights' => 'array'];
    }

    public function curatorMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'curator_media_id');
    }

    public function bannerMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_media_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return MediaUrl::versioned($this->curatorMedia);
    }

    public function getBannerUrlAttribute(): ?string
    {
        return MediaUrl::versioned($this->bannerMedia);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
