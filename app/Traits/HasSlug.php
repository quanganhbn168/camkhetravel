<?php

namespace App\Traits;

use App\Models\Slug;
use App\Support\Localization\LanguageCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

trait HasSlug
{
    protected ?string $requestedSlug = null;

    public function slugs(): MorphMany
    {
        return $this->morphMany(Slug::class, 'sluggable');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if ($field && $field !== 'slug') {
            return $this->newQuery()->where($field, $value)->first();
        }

        $locale = app()->getLocale();
        $defaultLocale = app(LanguageCatalog::class)->defaultCode();

        return $this->newQuery()
            ->whereHas('slugs', fn ($query) => $query
                ->where('slug', $value)
                ->whereIn('locale', array_unique([$locale, $defaultLocale])))
            ->first();
    }

    public function getSlugAttribute(mixed $value = null): ?string
    {
        if (is_string($value) && $value !== '') {
            return $value;
        }

        if ($this->requestedSlug !== null) {
            return $this->requestedSlug;
        }

        $locale = app()->getLocale();
        $defaultLocale = app(LanguageCatalog::class)->defaultCode();

        if ($this->relationLoaded('slugs')) {
            return $this->slugs->firstWhere('locale', $locale)?->slug
                ?: $this->slugs->firstWhere('locale', $defaultLocale)?->slug;
        }

        return $this->exists
            ? $this->slugs()->where('locale', $locale)->value('slug')
                ?: $this->slugs()->where('locale', $defaultLocale)->value('slug')
            : null;
    }

    public function setSlugAttribute(mixed $value): void
    {
        $slug = Str::slug((string) $value);

        $this->requestedSlug = $slug !== '' ? $slug : null;
    }

    public function pullRequestedSlug(): ?string
    {
        $slug = $this->requestedSlug;
        $this->requestedSlug = null;

        return $slug;
    }

    public function getSlugSourceValue(): string
    {
        foreach (['title', 'name'] as $attribute) {
            if (filled($this->getAttribute($attribute))) {
                return (string) $this->getAttribute($attribute);
            }
        }

        return '';
    }
}
