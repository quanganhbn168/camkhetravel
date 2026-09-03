<?php

namespace App\Models;

use App\Traits\HasBniMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;

class BniActivity extends Model implements HasMedia
{
    use HasBniMedia;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BniEvent::class, 'bni_event_id');
    }

    public function galleryItems(): HasMany
    {
        return $this->hasMany(BniGalleryItem::class)->orderBy('sort_order');
    }

    protected function bniImageCollections(): array
    {
        return ['image'];
    }
}
