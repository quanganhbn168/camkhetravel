<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BniScheduleDay extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BniEvent::class, 'bni_event_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BniScheduleItem::class)->orderBy('sort_order')->orderBy('starts_at');
    }
}
