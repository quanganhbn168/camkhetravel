<?php

use App\Models\BniActivity;
use App\Models\BniArticle;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniEventSlide;
use App\Models\BniGalleryItem;
use App\Support\Bni\BniMediaService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('curator') || ! Schema::hasTable('media')) {
            return;
        }

        $mappings = [
            [BniEvent::class, 'bni_events', 'hero_media_id', 'hero'],
            [BniEvent::class, 'bni_events', 'video_poster_media_id', 'video_poster'],
            [BniEvent::class, 'bni_events', 'video_media_id', 'video'],
            [BniEventSlide::class, 'bni_event_slides', 'media_id', 'image'],
            [BniChapter::class, 'bni_chapters', 'logo_media_id', 'logo'],
            [BniChapter::class, 'bni_chapters', 'cover_media_id', 'cover'],
            [BniChapter::class, 'bni_chapters', 'video_media_id', 'video'],
            [BniActivity::class, 'bni_activities', 'media_id', 'image'],
            [BniGalleryItem::class, 'bni_gallery_items', 'media_id', 'image'],
            [BniArticle::class, 'bni_articles', 'cover_media_id', 'cover'],
        ];

        foreach ($mappings as [$modelClass, $table, $column, $collection]) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                continue;
            }

            DB::table($table)
                ->whereNotNull($column)
                ->select(['id', $column])
                ->orderBy('id')
                ->chunkById(50, function ($rows) use ($modelClass, $column, $collection): void {
                    foreach ($rows as $row) {
                        $model = $modelClass::query()->find($row->id);

                        if ($model) {
                            app(BniMediaService::class)->importLegacyCuratorMedia(
                                $model,
                                (int) $row->{$column},
                                $collection,
                            );
                        }
                    }
                });
        }
    }

    public function down(): void
    {
        // Cột Curator cũ vẫn được giữ nguyên để rollback không làm mất liên kết media cũ.
    }
};
