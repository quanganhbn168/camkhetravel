<?php

namespace App\Observers;

use App\Models\Slug;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugObserver
{
    public function saved(Model $model): void
    {
        if (! method_exists($model, 'slugs')) {
            return;
        }

        $requestedSlug = method_exists($model, 'pullRequestedSlug')
            ? $model->pullRequestedSlug()
            : null;
        $locale = app()->getLocale();
        $currentSlug = $model->slugs()->where('locale', $locale)->first();

        if ($requestedSlug === null && $currentSlug) {
            return;
        }

        $baseSlug = Str::slug($requestedSlug ?: $model->getSlugSourceValue());

        if ($baseSlug === '') {
            return;
        }

        $model->slugs()->updateOrCreate(
            ['locale' => $locale],
            ['slug' => $this->makeUniqueSlug($baseSlug, $model, $locale)],
        );
    }

    public function deleted(Model $model): void
    {
        if (method_exists($model, 'slugs')) {
            $model->slugs()->delete();
        }
    }

    private function makeUniqueSlug(string $baseSlug, Model $model, string $locale): string
    {
        $slug = $baseSlug;
        $suffix = 2;

        while ($this->isReserved($slug) || Slug::query()
            ->where('slug', $slug)
            ->where('locale', $locale)
            ->where(function ($query) use ($model): void {
                $query->where('sluggable_type', '!=', $model->getMorphClass())
                    ->orWhere('sluggable_id', '!=', $model->getKey());
            })
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function isReserved(string $slug): bool
    {
        return in_array($slug, [
            'dich-vu',
            'du-an',
            'bang-gia',
            'gioi-thieu',
            'lien-he',
            'tin-tuc',
            'blog',
            'sitemap.xml',
            'robots.txt',
        ], true);
    }
}
