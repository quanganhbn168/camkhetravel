<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BniEventPrize extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['highlight' => 'boolean'];
    }

    public function landing(): BelongsTo
    {
        return $this->belongsTo(BniEventLanding::class, 'bni_event_landing_id');
    }
}
