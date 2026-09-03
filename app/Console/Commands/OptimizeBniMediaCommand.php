<?php

namespace App\Console\Commands;

use App\Models\BniActivity;
use App\Models\BniArticle;
use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\BniEventSlide;
use App\Models\BniGalleryItem;
use App\Support\Bni\BniMediaService;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OptimizeBniMediaCommand extends Command
{
    protected $signature = 'bni:optimize-media';

    protected $description = 'Tạo lại bản WebP chất lượng cao cho toàn bộ ảnh thuộc hệ thống BNI';

    public function handle(BniMediaService $mediaService): int
    {
        $converted = 0;
        $modelTypes = collect([
            BniEvent::class,
            BniEventSlide::class,
            BniChapter::class,
            BniActivity::class,
            BniArticle::class,
            BniGalleryItem::class,
        ])->map(fn (string $modelClass): string => (new $modelClass)->getMorphClass());

        Media::query()
            ->whereIn('model_type', $modelTypes)
            ->orderBy('id')
            ->chunkById(100, function ($mediaItems) use ($mediaService, &$converted): void {
                foreach ($mediaItems as $media) {
                    $converted += (int) $mediaService->regenerateWebp($media);
                }
            });

        $this->info("Đã tạo lại {$converted} bản WebP BNI ở chất lượng ".BniMediaService::WEBP_QUALITY.'. Ảnh gốc vẫn được giữ nguyên.');

        return self::SUCCESS;
    }
}
