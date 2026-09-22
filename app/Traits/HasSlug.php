<?php

namespace App\Traits;

use App\Models\Slug;
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

        return $this->newQuery()
            ->whereHas('slugs', fn ($query) => $query->where('slug', $value))
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

        if ($this->relationLoaded('slugs')) {
            return $this->slugs->first()?->slug;
        }

        return $this->exists
            ? $this->slugs()->value('slug')
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
