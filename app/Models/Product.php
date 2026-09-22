<?php

namespace App\Models;

use App\Support\Media\MediaUrl;
use App\Traits\HasComments;
use App\Traits\HasFaqs;
use App\Traits\HasSeoImage;
use App\Traits\HasSlug;
use App\Traits\HasTags;
use Awcodes\Curator\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Product extends Model
{
    use HasComments, HasFactory, HasFaqs, HasSlug, HasTags;
    use HasSeoImage;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(function (Product $product): void {
            if ($product->product_category_id !== null && ! ProductCategory::query()->whereKey($product->product_category_id)->doesntHave('children')->exists()) {
                throw ValidationException::withMessages([
                    'product_category_id' => 'Chỉ được chọn danh mục không có danh mục con.',
                ]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function curatorMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'curator_media_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return MediaUrl::resolve($this->curatorMedia);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(fn (Builder $query) => $query
            ->whereNull('published_at')
            ->orWhere('published_at', '<=', now()));
    }
}
