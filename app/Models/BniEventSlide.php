<?php

namespace App\Models;

use App\Traits\HasBniMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;

class BniEventSlide extends Model implements HasMedia
{
    use HasBniMedia;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BniEvent::class, 'bni_event_id');
    }

    protected function bniImageCollections(): array
    {
        return ['image'];
    }
}
