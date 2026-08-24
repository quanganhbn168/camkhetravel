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
            'is_featured' => 'boolean',
            'completed_at' => 'date',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
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

    public function backstageLandings(): BelongsToMany
    {
        return $this->belongsToMany(Landing::class)->withTimestamps();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(fn (Builder $query) => $query
            ->whereNull('published_at')
            ->orWhere('published_at', '<=', now()));
    }
}
