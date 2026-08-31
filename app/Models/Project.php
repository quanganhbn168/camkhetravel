<?php

namespace App\Models;

use App\Traits\HasSlug;
use App\Traits\HasComments;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasComments, HasSlug;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'faq_items' => 'array',
            'is_featured' => 'boolean',
            'completed_at' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    public function curatorMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'curator_media_id');
    }

    public function backstageServices(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'project_service')->withTimestamps();
    }

    public function landingPages(): BelongsToMany
    {
        return $this->belongsToMany(LandingPage::class, 'landing_page_project')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function relatedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withTimestamps();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(fn (Builder $query) => $query
            ->whereNull('published_at')
            ->orWhere('published_at', '<=', now()));
    }
}
