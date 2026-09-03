<?php

namespace App\Models;

use App\Traits\GeneratesBniSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BniArticleCategory extends Model
{
    use GeneratesBniSlug;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(
            BniArticle::class,
            'bni_article_category_article',
        )->withPivot('sort_order');
    }

    protected function bniSlugSource(): string
    {
        return (string) $this->name;
    }
}
