<?php

namespace App\Console\Commands;

use App\Settings\WebsiteSettings;
use App\Support\Branding\FaviconService;
use Awcodes\Curator\Models\Media;
use Illuminate\Console\Command;

class SyncFaviconAssetsCommand extends Command
{
    protected $signature = 'favicon:sync';

    protected $description = 'Ghi đè bộ favicon cố định trong public từ favicon đã chọn trong Curator.';

    public function handle(WebsiteSettings $settings, FaviconService $favicons): int
    {
        $media = $settings->favicon_media_id
            ? Media::query()->find($settings->favicon_media_id)
            : null;

        if (! $media) {
            $this->components->info('Không có favicon Curator được chọn; website dùng bộ favicon mặc định trong public.');

            return self::SUCCESS;
        }

        $favicons->sync($media);

        $this->components->info("Đã ghi đè bộ favicon cố định từ media Curator #{$media->getKey()}.");

        return self::SUCCESS;
    }
}
