<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BniEvent extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_featured' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function heroMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'hero_media_id');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(BniChapter::class)->orderBy('sort_order');
    }

    public function purposes(): HasMany
    {
        return $this->hasMany(BniPurpose::class)->orderBy('sort_order');
    }

    public function scheduleItems(): HasMany
    {
        return $this->hasMany(BniScheduleItem::class)->orderBy('day_number')->orderBy('sort_order');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(BniActivity::class)->orderBy('sort_order');
    }

    public function galleries(): HasMany
    {
        return $this->hasMany(BniGalleryItem::class)->orderBy('sort_order');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(BniArticle::class)->latest('published_at');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(BniRegistration::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }
}
