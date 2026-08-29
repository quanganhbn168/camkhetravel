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
            'faq_items' => 'array',
            'sections' => 'array',
            'template_settings' => 'array',
            'theme_settings' => 'array',
            'is_featured' => 'boolean',
            'show_header' => 'boolean',
            'show_footer' => 'boolean',
            'tracking_enabled' => 'boolean',
            'campaign_starts_at' => 'datetime',
            'campaign_ends_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LandingCategory::class, 'landing_category_id');
    }

    public function landingTemplate(): BelongsTo
    {
        return $this->belongsTo(LandingTemplate::class);
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

    public function events(): HasMany
    {
        return $this->hasMany(LandingEvent::class);
    }

    public function contactRequests(): HasMany
    {
        return $this->hasMany(ContactRequest::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(fn (Builder $query) => $query
            ->whereNull('published_at')
            ->orWhere('published_at', '<=', now()));
    }
}
