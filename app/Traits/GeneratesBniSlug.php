<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneratesBniSlug
{
    protected static function bootGeneratesBniSlug(): void
    {
        static::creating(function (Model $model): void {
            if (blank($model->getAttribute('slug'))) {
                $model->setAttribute('slug', $model->generateUniqueBniSlug($model->bniSlugSource()));
            }
        });
    }

    abstract protected function bniSlugSource(): string;

    protected function generateUniqueBniSlug(string $source): string
    {
        $baseSlug = Str::limit(Str::slug($source) ?: 'bni', 240, '');
        $slug = $baseSlug;
        $suffix = 2;

        while (static::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
