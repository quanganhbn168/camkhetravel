<?php

namespace App\Models;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePricing extends Model
{
    protected $guarded = [];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function sourceMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'source_media_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(PricingPackage::class)->orderBy('sort_order');
    }
}
