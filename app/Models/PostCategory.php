<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PostCategory extends Model
{
    use HasSlug;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function legacyTerm(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'legacy_term_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class)->withPivot('sort_order');
    }
}
