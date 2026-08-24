<?php

namespace App\Console\Commands;

use App\Models\MediaAsset;
use App\Models\Landing;
use App\Models\Post;
use App\Models\Project;
use Awcodes\Curator\Models\Media;
use Illuminate\Console\Command;

class SyncLegacyMediaToCuratorCommand extends Command
{
    protected $signature = 'cms:sync-legacy-media-to-curator';

    protected $description = 'Đăng ký media self-host đã import vào Curator mà không nhân bản file.';

    public function handle(): int
    {
        $created = 0;
        $linked = 0;

        Media::withoutEvents(function () use (&$created, &$linked): void {
            MediaAsset::query()
                ->where('disk', 'public')
                ->whereNotNull('file_path')
                ->where('localization_status', 'localized')
                ->orderBy('id')
                ->cursor()
                ->each(function (MediaAsset $asset) use (&$created, &$linked): void {
                    $media = Media::query()->firstOrCreate(
                        [
                            'disk' => $asset->disk,
                            'path' => $asset->file_path,
                        ],
                        [
                            'directory' => trim((string) dirname($asset->file_path), '.'),
                            'visibility' => 'public',
                            'name' => pathinfo((string) $asset->file_path, PATHINFO_FILENAME),
                            'width' => $asset->width,
                            'height' => $asset->height,
                            'size' => $asset->file_size,
                            'type' => $asset->mime_type ?: 'application/octet-stream',
                            'ext' => strtolower(pathinfo((string) $asset->file_path, PATHINFO_EXTENSION)),
                            'alt' => $asset->effective_alt_text ?: $asset->alt_text,
                            'title' => $asset->title,
                            'description' => $asset->caption,
                            'caption' => $asset->caption,
                        ],
                    );

                    $created += $media->wasRecentlyCreated ? 1 : 0;
                    $linked += $this->linkLegacyAsset($asset, $media->id);
                });
        });

        $this->components->info("Curator: {$created} media mới, {$linked} liên kết nội dung đã gắn.");

        return self::SUCCESS;
    }

    private function linkLegacyAsset(MediaAsset $asset, int $mediaId): int
    {
        $updated = 0;

        foreach ([Landing::class, Project::class, Post::class] as $model) {
            $updated += $model::query()
                ->where('legacy_media_asset_id', $asset->id)
                ->whereNull('curator_media_id')
                ->update(['curator_media_id' => $mediaId]);
        }

        return $updated;
    }
}
