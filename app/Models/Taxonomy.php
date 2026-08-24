<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Taxonomy extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_hierarchical' => 'boolean',
            'is_public' => 'boolean',
        ];
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class);
    }
}
