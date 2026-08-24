<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'content_data' => 'array',
            'seo_robots' => 'array',
            'seo_score' => 'integer',
            'is_pillar_content' => 'boolean',
            'exclude_from_sitemap' => 'boolean',
            'structured_data' => 'array',
            'effective_seo' => 'array',
            'needs_seo_review' => 'boolean',
            'legacy_meta' => 'array',
            'source_snapshot' => 'array',
            'import_locked_fields' => 'array',
            'published_at' => 'datetime',
            'content_modified_at' => 'datetime',
            'seo_materialized_at' => 'datetime',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function terms(): BelongsToMany
    {
        return $this->belongsToMany(Term::class)->withPivot('position');
    }
}
