<?php

namespace App\Traits;

use App\Support\Categories\CategoryTree;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

trait HasCategoryTree
{
    public static function bootHasCategoryTree(): void
    {
        static::saving(function ($category): void {
            if ($category->parent_id !== null && ! array_key_exists($category->parent_id, CategoryTree::parentOptions($category::class, $category))) {
                throw ValidationException::withMessages(['parent_id' => 'Danh mục cha không hợp lệ. Không thể chọn chính mình, con cháu hoặc danh mục đang chứa sản phẩm.']);
            }
        });
        static::deleting(function ($category): void {
            if ($category->children()->exists()) {
                throw ValidationException::withMessages(['parent_id' => 'Hãy chuyển hoặc xóa danh mục con trước khi xóa danh mục cha.']);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
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
        return MediaUrl::resolve($this->curatorMedia);
    }

    public function getBannerUrlAttribute(): ?string
    {
        return MediaUrl::resolve($this->bannerMedia);
    }

    public function subtreeIds(bool $activeOnly = false): array
    {
        return CategoryTree::subtreeIds(static::class, (int) $this->getKey(), $activeOnly);
    }
}
