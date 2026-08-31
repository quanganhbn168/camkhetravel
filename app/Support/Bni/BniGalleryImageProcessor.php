<?php

namespace App\Support\Bni;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class BniGalleryImageProcessor
{
    private const MAX_EDGE = 2400;

    /** @return array{path: string, directory: string, name: string, width: int, height: int, size: int, type: string, ext: string} */
    public function store(UploadedFile $upload): array
    {
        $contents = file_get_contents($upload->getRealPath());
        $source = is_string($contents) ? @imagecreatefromstring($contents) : false;

        if ($source === false) {
            throw new RuntimeException('Tệp tải lên không phải hình ảnh hợp lệ.');
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $scale = min(1, self::MAX_EDGE / max($sourceWidth, $sourceHeight));
        $width = max(1, (int) round($sourceWidth * $scale));
        $height = max(1, (int) round($sourceHeight * $scale));
        $canvas = imagecreatetruecolor($width, $height);

        if ($canvas === false) {
            throw new RuntimeException('Không thể chuẩn hóa hình ảnh tải lên.');
        }

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);

        $directory = 'media/bni/community/'.now()->format('Y/m');
        $name = (string) Str::uuid();
        $extension = function_exists('imagewebp') ? 'webp' : 'png';
        $mimeType = $extension === 'webp' ? 'image/webp' : 'image/png';
        $path = $directory.'/'.$name.'.'.$extension;
        $disk = Storage::disk('public');
        $disk->makeDirectory($directory);
        $absolutePath = $disk->path($path);

        $written = $extension === 'webp'
            ? imagewebp($canvas, $absolutePath, 82)
            : imagepng($canvas, $absolutePath, 7);

        imagedestroy($source);
        imagedestroy($canvas);

        if (! $written) {
            throw new RuntimeException('Không thể ghi hình ảnh đã chuẩn hóa.');
        }

        return [
            'path' => $path,
            'directory' => $directory,
            'name' => $name,
            'width' => $width,
            'height' => $height,
            'size' => $disk->size($path),
            'type' => $mimeType,
            'ext' => $extension,
        ];
    }
}
