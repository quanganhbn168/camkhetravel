<?php

namespace App\Models;

use App\Traits\HasComments;
use App\Traits\HasFaqs;
use App\Traits\HasSeoImage;
use App\Traits\HasSlug;
use App\Traits\HasTags;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasComments, HasFactory, HasFaqs, HasSlug, HasTags;
    use HasSeoImage;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'backstage_gallery' => 'array',
            'process_items' => 'array',
            'benefit_items' => 'array',
            'stats_items' => 'array',
            'reference_videos' => 'array',
            'commitment_items' => 'array',
            'is_featured' => 'boolean',
            'is_home' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function curatorMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'curator_media_id');
    }

    public function bannerVideoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_video_media_id');
    }

    public function processBackgroundMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'process_background_media_id');
    }

    public function commitmentMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'commitment_media_id');
    }

    public function backstageProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_service')->withTimestamps();
    }


    public function contactRequests(): HasMany
    {
        return $this->hasMany(ContactRequest::class, 'service_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(fn (Builder $query) => $query
            ->whereNull('published_at')
            ->orWhere('published_at', '<=', now()));
    }
}
