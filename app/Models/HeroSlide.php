<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroSlide extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function curatorMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'curator_media_id');
    }

    public function videoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'video_media_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function resolvedVideoUrl(): ?string
    {
        if ($this->video_source === 'upload') {
            return $this->videoMedia?->url;
        }

        if ($this->video_source !== 'youtube' || ! filter_var($this->video_url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $host = strtolower((string) parse_url($this->video_url, PHP_URL_HOST));

        return in_array($host, [
            'youtu.be',
            'youtube.com',
            'www.youtube.com',
            'm.youtube.com',
            'youtube-nocookie.com',
            'www.youtube-nocookie.com',
        ], true) ? $this->video_url : null;
    }
}
