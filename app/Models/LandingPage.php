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

class LandingPage extends Model
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

    public function landingTemplate(): BelongsTo
    {
        return $this->belongsTo(LandingTemplate::class);
    }

    public function curatorMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'curator_media_id');
    }

    public function pricingPlans(): HasMany
    {
        return $this->hasMany(PricingPlan::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'landing_page_project')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function serviceCategories(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'landing_page_service_category')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'landing_page_service')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'landing_page_post')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function events(): HasMany
    {
        return $this->hasMany(LandingEvent::class, 'landing_page_id');
    }

    public function contactRequests(): HasMany
    {
        return $this->hasMany(ContactRequest::class, 'landing_page_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(fn (Builder $query) => $query
            ->whereNull('published_at')
            ->orWhere('published_at', '<=', now()));
    }
}
