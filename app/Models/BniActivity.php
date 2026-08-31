<?php

namespace App\Models;

use App\Traits\ConvertsBniMediaToWebp;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BniActivity extends Model
{
    use ConvertsBniMediaToWebp;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BniEvent::class, 'bni_event_id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function galleryItems(): HasMany
    {
        return $this->hasMany(BniGalleryItem::class)->orderBy('sort_order');
    }

    protected function bniWebpMediaAttributes(): array
    {
        return ['media_id'];
    }
}
