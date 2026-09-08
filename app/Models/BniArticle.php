<?php

namespace App\Models;

use App\Traits\GeneratesBniSlug;
use App\Traits\HasBniMedia;
use App\Traits\HasComments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\MediaLibrary\HasMedia;

class BniArticle extends Model implements HasMedia
{
    use GeneratesBniSlug;
    use HasBniMedia;
    use HasComments;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(BniEvent::class, 'bni_event_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(BniChapter::class, 'bni_chapter_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            BniArticleCategory::class,
            'bni_article_category_article',
        )->withPivot('sort_order');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(BniReaction::class, 'reactable');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where(fn (Builder $query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    protected function bniSlugSource(): string
    {
        return (string) $this->title;
    }

    protected function bniImageCollections(): array
    {
        return ['cover', 'seo_image'];
    }
}
