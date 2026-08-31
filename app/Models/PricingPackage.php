<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricingPackage extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'promotion_value' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function servicePricing(): BelongsTo
    {
        return $this->belongsTo(ServicePricing::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PricingPackageItem::class)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function promotionPrice(): ?int
    {
        if ($this->list_price === null || $this->promotion_type === null || $this->promotion_value === null) {
            return null;
        }

        if ($this->promotion_type === 'fixed_price') {
            return max(0, (int) round((float) $this->promotion_value));
        }

        if ($this->promotion_type === 'percent') {
            return max(0, (int) round((float) $this->list_price * (1 - min(100, max(0, (float) $this->promotion_value)) / 100)));
        }

        return null;
    }

    public function hasPromotion(): bool
    {
        $promotionPrice = $this->promotionPrice();

        return $promotionPrice !== null && $this->list_price !== null && $promotionPrice < (int) $this->list_price;
    }
}
