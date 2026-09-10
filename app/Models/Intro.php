<?php

namespace App\Models;

use App\Support\Media\MediaUrl;
use App\Traits\HasSlug;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Intro extends Model
{
    use HasSlug;

    protected $guarded = [];

    protected $attributes = ['kind' => 'article', 'is_active' => false];

    protected $casts = ['is_active' => 'boolean', 'published_at' => 'datetime'];

    public function curatorMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'curator_media_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('kind', 'article')->where('is_active', true)
            ->whereHas('slugs', fn (Builder $slugs): Builder => $slugs->where('slug', '!=', ''))
            ->where(fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function shouldSyncSlug(): bool
    {
        return $this->kind === 'article';
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('kind', 'block')->where('is_active', true)->orderBy('sort_order');
    }

    public function getUrlAttribute(): string
    {
        return route('intros.show', ['slug' => $this->slug]);
    }

    public function getImageUrlAttribute(): ?string
    {
        return MediaUrl::resolve($this->curatorMedia);
    }
}
