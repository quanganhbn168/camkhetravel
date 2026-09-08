<?php

namespace App\Support\Bni;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Conversions\FileManipulator;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class BniMediaService
{
    public const WEBP_CONVERSION = 'webp';

    public const WEBP_QUALITY = 100;

    private const IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/svg+xml',
    ];

    private const RASTER_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    private const VIDEO_MIME_TYPES = [
        'video/mp4',
        'video/webm',
        'video/quicktime',
    ];

    /**
     * @param  array<int, string>  $imageCollections
     * @param  array<int, string>  $videoCollections
     */
    public function registerCollections(HasMedia $model, array $imageCollections, array $videoCollections = []): void
    {
        foreach ($imageCollections as $collection) {
            $model->addMediaCollection($collection)
                ->useDisk('public')
                ->acceptsMimeTypes(self::IMAGE_MIME_TYPES)
                ->singleFile();
        }

        foreach ($videoCollections as $collection) {
            $model->addMediaCollection($collection)
                ->useDisk('public')
                ->acceptsMimeTypes(self::VIDEO_MIME_TYPES)
                ->singleFile();
        }
    }

    /** @param array<int, string> $collections */
    public function registerWebpConversion(HasMedia $model, array $collections, ?Media $media = null): void
    {
        if ($media && ! in_array(strtolower((string) $media->mime_type), self::RASTER_MIME_TYPES, true)) {
            return;
        }

        $model->addMediaConversion(self::WEBP_CONVERSION)
            ->format('webp')
            ->quality(self::WEBP_QUALITY)
            ->performOnCollections(...$collections)
            ->nonQueued();
    }

    public function url(?HasMedia $model, string $collection, bool $preferWebp = true): ?string
    {
        $media = $model?->getFirstMedia($collection);

        if (! $media) {
            return null;
        }

        $conversion = $preferWebp && $media->hasGeneratedConversion(self::WEBP_CONVERSION)
            ? self::WEBP_CONVERSION
            : '';

        if ($conversion !== '' && ! Storage::disk($media->conversions_disk ?: $media->disk)->exists($media->getPathRelativeToRoot($conversion))) {
            $conversion = '';
        }

        if ($conversion === '' && ! Storage::disk($media->disk)->exists($media->getPathRelativeToRoot())) {
            return null;
        }

        $url = $media->getUrl($conversion);

        if ($url === '') {
            return null;
        }

        $separator = str_contains($url, '?') ? '&' : '?';
        $version = $media->updated_at?->getTimestamp() ?? $media->getKey();

        return "{$url}{$separator}v={$version}";
    }

    public function first(?HasMedia $model, string $collection): ?Media
    {
        return $model?->getFirstMedia($collection);
    }

    /** @param array<string, mixed> $customProperties */
    public function attachUpload(
        HasMedia $model,
        UploadedFile $upload,
        string $collection,
        ?string $name = null,
        array $customProperties = [],
    ): Media {
        return $model->addMedia($upload)
            ->usingName($name ?: pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME))
            ->withCustomProperties($customProperties)
            ->toMediaCollection($collection, 'public');
    }

    public function importLegacyCuratorMedia(HasMedia $model, ?int $curatorId, string $collection): ?Media
    {
        if (! $curatorId || $model->getFirstMedia($collection)) {
            return null;
        }

        $legacy = DB::table('curator')->where('id', $curatorId)->first();

        if (! $legacy || ! Storage::disk($legacy->disk)->exists($legacy->path)) {
            return null;
        }

        try {
            return $model->addMediaFromDisk($legacy->path, $legacy->disk)
                ->preservingOriginal()
                ->usingName($legacy->title ?: $legacy->name)
                ->usingFileName(basename($legacy->path))
                ->withCustomProperties(array_filter([
                    'alt' => $legacy->alt,
                    'caption' => $legacy->caption,
                    'legacy_curator_id' => $legacy->id,
                ], fn ($value): bool => filled($value)))
                ->toMediaCollection($collection, 'public');
        } catch (Throwable $exception) {
            Log::warning('Không thể chuyển media BNI cũ sang Spatie Media Library.', [
                'model' => $model::class,
                'model_id' => $model->getKey(),
                'curator_id' => $curatorId,
                'collection' => $collection,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    public function regenerateWebp(Media $media): bool
    {
        if (! in_array(strtolower((string) $media->mime_type), self::RASTER_MIME_TYPES, true)) {
            return false;
        }

        app(FileManipulator::class)->createDerivedFiles($media, [self::WEBP_CONVERSION]);
        $media->refresh();

        return $media->hasGeneratedConversion(self::WEBP_CONVERSION);
    }
}
