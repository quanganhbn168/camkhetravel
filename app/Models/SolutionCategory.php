<?php

namespace App\Models;

use App\Support\Media\MediaUrl;
use App\Traits\HasSeoImage;
use App\Traits\HasSlug;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class SolutionCategory extends Model
{
    use HasSeoImage, HasSlug;

    protected $guarded = ['id'];

    protected $attributes = ['sort_order' => 0, 'is_active' => false];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    protected static function booted(): void
    {
        static::deleting(function (SolutionCategory $category): void {
            if ($category->solutions()->exists()) {
                throw ValidationException::withMessages([
                    'solution_category_id' => 'Chuyển hoặc xóa các giải pháp trong danh mục trước khi xóa danh mục.',
                ]);
            }
        });
    }

    public function solutions(): HasMany
    {
        return $this->hasMany(Solution::class, 'solution_category_id');
    }

    public function curatorMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'curator_media_id');
    }

    public function bannerMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_media_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return MediaUrl::versioned($this->curatorMedia);
    }

    public function getBannerUrlAttribute(): ?string
    {
        return MediaUrl::versioned($this->bannerMedia);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
