<?php

namespace App\Console\Commands;

use App\Models\BniActivity;
use App\Models\BniArticle;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniEventSlide;
use App\Models\BniGalleryItem;
use App\Support\Bni\BniMediaOptimizer;
use Illuminate\Console\Command;

class OptimizeBniMediaCommand extends Command
{
    protected $signature = 'bni:optimize-media';

    protected $description = 'Chuẩn hóa ảnh raster của slide, tin tức, chapter, hoạt động và thư viện BNI sang WebP';

    public function handle(BniMediaOptimizer $optimizer): int
    {
        $converted = 0;

        $targets = [
            BniEvent::class => ['video_poster_media_id'],
            BniEventSlide::class => ['media_id'],
            BniChapter::class => ['logo_media_id', 'cover_media_id'],
            BniActivity::class => ['media_id'],
            BniArticle::class => ['cover_media_id'],
            BniGalleryItem::class => ['media_id'],
        ];

        foreach ($targets as $modelClass => $attributes) {
            $modelClass::query()->chunkById(100, function ($models) use ($attributes, $optimizer, &$converted): void {
                foreach ($models as $model) {
                    foreach ($attributes as $attribute) {
                        $sourceId = (int) $model->getAttribute($attribute);

                        if ($sourceId < 1) {
                            continue;
                        }

                        $optimizedId = $optimizer->optimize($sourceId);

                        if ($optimizedId === $sourceId) {
                            continue;
                        }

                        $model->setAttribute($attribute, $optimizedId);
                        $converted++;
                    }

                    if ($model->isDirty()) {
                        $model->save();
                    }
                }
            });
        }

        $this->info("Đã chuẩn hóa {$converted} liên kết ảnh BNI sang WebP. Ảnh SVG/GIF/video và banner dùng chung với thư mời được giữ nguyên.");

        return self::SUCCESS;
    }
}
