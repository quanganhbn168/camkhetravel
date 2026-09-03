<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BniScheduleItem extends Model
{
    protected $guarded = [];

    public function day(): BelongsTo
    {
        return $this->belongsTo(BniScheduleDay::class, 'bni_schedule_day_id');
    }
}
