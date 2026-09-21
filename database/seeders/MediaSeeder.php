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

        foreach ([
            'hero' => 'installation-team.png', 'equipment' => 'equipment.png', 'facility' => 'facility.png',
            'engineering' => 'engineering-team.png', 'warehouse' => 'warehouse.png', 'technician' => 'technician.png',
        ] as $name => $filename) {
            $source = base_path('resources/content/site/'.$filename);

            if (! is_file($source)) {
                continue;
            }

            $path = 'media/site/'.$filename;
            $disk->put($path, file_get_contents($source));
            $dimensions = @getimagesize($disk->path($path)) ?: [null, null, 'mime' => 'image/png'];

            Media::query()->updateOrCreate(['disk' => 'public', 'path' => $path], [
                'directory' => 'media/site', 'visibility' => 'public', 'name' => pathinfo($filename, PATHINFO_FILENAME),
                'width' => $dimensions[0] ?? null, 'height' => $dimensions[1] ?? null, 'size' => filesize($source) ?: 0,
                'type' => $dimensions['mime'] ?? 'image/png', 'ext' => pathinfo($filename, PATHINFO_EXTENSION),
                'alt' => 'Ảnh minh họa '.$name, 'title' => 'Ảnh minh họa '.$name,
            ]);
        }
    }

    public static function id(string $name): ?int
    {
        return Media::query()->where('disk', 'public')->where('path', 'media/site/'.$name.'.png')->value('id');
    }
}
