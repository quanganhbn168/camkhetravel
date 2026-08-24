<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroSlideTranslation extends Model
{
    protected $guarded = [];

    public function heroSlide(): BelongsTo
    {
        return $this->belongsTo(HeroSlide::class);
    }
}
