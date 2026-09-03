<?php

namespace App\Traits;

use App\Support\Bni\BniMediaService;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasBniMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        app(BniMediaService::class)->registerCollections(
            $this,
            $this->bniImageCollections(),
            $this->bniVideoCollections(),
        );
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        app(BniMediaService::class)->registerWebpConversion(
            $this,
            $this->bniImageCollections(),
            $media,
        );
    }

    public function bniMediaUrl(string $collection, bool $preferWebp = true): ?string
    {
        return app(BniMediaService::class)->url($this, $collection, $preferWebp);
    }

    public function bniFirstMedia(string $collection): ?Media
    {
        return app(BniMediaService::class)->first($this, $collection);
    }

    /** @return array<int, string> */
    abstract protected function bniImageCollections(): array;

    /** @return array<int, string> */
    protected function bniVideoCollections(): array
    {
        return [];
    }
}
