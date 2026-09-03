<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $introVideoId = Schema::hasTable('curator')
            ? DB::table('curator')
                ->where('path', 'media/library/2023/01/tht-intro.mp4')
                ->where('type', 'video/mp4')
                ->value('id')
            : null;

        $this->migrator->add('about.video_source', $introVideoId ? 'upload' : '');
        $this->migrator->add('about.video_youtube_url', '');
        $this->migrator->add('about.video_media_id', $introVideoId ? (int) $introVideoId : null);
        $this->migrator->update('about.services_title', function (mixed $titles): array {
            $titles = is_array($titles) ? $titles : [];
            $currentTitle = trim((string) ($titles['vi'] ?? ''));

            if ($currentTitle === '' || in_array($currentTitle, ['Chúng tôi làm gì?', 'Hệ sinh thái dịch vụ'], true)) {
                $titles['vi'] = 'Hệ sinh thái dịch vụ của chúng tôi';
            }

            return $titles;
        });
    }
};
