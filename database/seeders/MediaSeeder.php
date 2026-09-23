<?php

namespace Database\Seeders;

use Awcodes\Curator\Models\Media;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

final class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $disk = Storage::disk('public');
        $source = public_path('images/no-image.svg');

        if (! is_file($source)) {
            throw new \RuntimeException('Thiếu ảnh mặc định public/images/no-image.svg.');
        }

        $path = 'media/site/no-image.svg';
        $disk->put($path, file_get_contents($source));

        Media::query()->updateOrCreate(['disk' => 'public', 'path' => $path], [
            'directory' => 'media/site', 'visibility' => 'public', 'name' => 'no-image',
            'width' => 960, 'height' => 600, 'size' => filesize($source) ?: 0,
            'type' => 'image/svg+xml', 'ext' => 'svg',
            'alt' => 'Ảnh mặc định CamKheTravel', 'title' => 'Ảnh mặc định CamKheTravel',
        ]);
    }

    public static function id(string $name): ?int
    {
        return $name === 'no-image'
            ? Media::query()->where('disk', 'public')->where('path', 'media/site/no-image.svg')->value('id')
            : null;
    }
}
