<?php

namespace App\Models;

use App\Traits\GeneratesBniSlug;
use App\Traits\HasBniMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    public function scheduleDays(): HasMany
    {
        return $this->hasMany(BniScheduleDay::class)->orderBy('sort_order')->orderBy('event_date');
    }

    public function video(): HasOne
    {
        return $this->hasOne(BniEventVideo::class);
    }

    public function landing(): HasOne
    {
        return $this->hasOne(BniEventLanding::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(BniActivity::class)->orderBy('sort_order');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(BniContact::class)->whereNull('bni_chapter_id')->orderByDesc('is_primary')->orderBy('sort_order');
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
        return ['hero'];
    }
}
