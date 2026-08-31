<?php

namespace App\Support\Bni;

use Awcodes\Curator\Models\Media;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BniMediaOptimizer
{
    private const MAX_EDGE = 2400;

    private const QUALITY = 82;

    public function optimize(int $mediaId): int
    {
        $media = Media::query()->find($mediaId);

        if (! $media || in_array(strtolower((string) $media->ext), ['webp', 'svg', 'gif'], true)) {
            return $mediaId;
        }

        if (! str_starts_with(strtolower((string) $media->type), 'image/') || ! function_exists('imagewebp')) {
            return $mediaId;
        }

        try {
            $disk = Storage::disk($media->disk);
            $name = 'bni-'.$media->getKey().'-'.($media->updated_at?->getTimestamp() ?? 'source');
            $existing = Media::query()
                ->where('disk', $media->disk)
                ->where('name', $name)
                ->where('ext', 'webp')
                ->first();

            if ($existing && $disk->exists($existing->path)) {
                return (int) $existing->getKey();
            }

            if (! $disk->exists($media->path)) {
                return $mediaId;
            }

            $source = @imagecreatefromstring($disk->get($media->path));

            if ($source === false) {
                return $mediaId;
            }

            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);
            $scale = min(1, self::MAX_EDGE / max($sourceWidth, $sourceHeight));
            $width = max(1, (int) round($sourceWidth * $scale));
            $height = max(1, (int) round($sourceHeight * $scale));
            $canvas = imagecreatetruecolor($width, $height);

            if ($canvas === false) {
                imagedestroy($source);

                return $mediaId;
            }

            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            imagefill($canvas, 0, 0, $transparent);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

            ob_start();
            $encoded = imagewebp($canvas, null, self::QUALITY);
            $contents = ob_get_clean();
            imagedestroy($source);
            imagedestroy($canvas);

            if (! $encoded || ! is_string($contents) || $contents === '') {
                return $mediaId;
            }

            $directory = 'media/bni/webp/'.now()->format('Y/m');
            $path = $directory.'/'.$name.'.webp';
            $visibility = $media->visibility ?: 'public';

            $disk->put($path, $contents, ['visibility' => $visibility]);

            return (int) Media::query()->create([
                'disk' => $media->disk,
                'directory' => $directory,
                'visibility' => $visibility,
                'name' => $name,
                'path' => $path,
                'width' => $width,
                'height' => $height,
                'size' => $disk->size($path),
                'type' => 'image/webp',
                'ext' => 'webp',
                'alt' => $media->alt,
                'title' => $media->title,
                'description' => $media->description,
                'caption' => $media->caption,
            ])->getKey();
        } catch (Throwable $exception) {
            Log::warning('Không thể chuẩn hóa ảnh BNI sang WebP.', [
                'media_id' => $mediaId,
                'message' => $exception->getMessage(),
            ]);

            return $mediaId;
        }
    }
}
