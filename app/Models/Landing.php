<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\HasSlug;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Landing extends Model
{
    use HasComments, HasSlug;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'backstage_gallery' => 'array',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LandingCategory::class, 'landing_category_id');
    }

    public function legacyContent(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class, 'legacy_content_item_id');
    }

    public function legacyMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'legacy_media_asset_id');
    }

    public function curatorMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'curator_media_id');
    }

    public function pricingMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'pricing_media_id');
    }

    public function pricingPlans(): HasMany
    {
        return $this->hasMany(PricingPlan::class);
    }

    public function backstageProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(fn (Builder $query) => $query
            ->whereNull('published_at')
            ->orWhere('published_at', '<=', now()));
    }
}
