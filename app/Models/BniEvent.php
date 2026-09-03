<?php

namespace App\Models;

use App\Traits\GeneratesBniSlug;
use App\Traits\HasBniMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;

class BniEvent extends Model implements HasMedia
{
    use GeneratesBniSlug;
    use HasBniMedia;

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

    public function chapters(): HasMany
    {
        return $this->hasMany(BniChapter::class)->orderBy('sort_order');
    }

    public function slides(): HasMany
    {
        return $this->hasMany(BniEventSlide::class)->orderBy('sort_order');
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

    protected function bniSlugSource(): string
    {
        return (string) $this->title;
    }

    protected function bniImageCollections(): array
    {
        return ['hero', 'video_poster'];
    }

    protected function bniVideoCollections(): array
    {
        return ['video'];
    }
}
