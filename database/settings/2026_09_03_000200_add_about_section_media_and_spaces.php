<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('about.story_image_media_id', $this->mediaId('media/library/2023/01/1672729584.webp'));
        $this->migrator->add('about.video_poster_media_id', $this->mediaId('media/library/2023/01/quay-phim-gioi-thieu-doanh-nghiep.png.webp'));
        $this->migrator->add('about.core_values_image_media_id', $this->mediaId('media/library/2023/01/1673253804-e1673255466732.jpg'));
        $this->migrator->add('about.team_title', ['vi' => 'Đội ngũ nhân sự']);
        $this->migrator->add('about.team_description', []);
        $this->migrator->add('about.team_image_media_id', $this->mediaId('media/library/2026/08/HNP09896-scaled.jpg'));
        $this->migrator->add('about.office_title', ['vi' => 'Văn phòng THT Media']);
        $this->migrator->add('about.office_description', []);
        $this->migrator->add('about.office_image_media_id', $this->mediaId('media/library/2023/01/studio.jpg'));

        $this->migrator->update('about.cta_title', function (mixed $titles): array {
            $titles = is_array($titles) ? $titles : [];
            $currentTitle = trim((string) ($titles['vi'] ?? ''));

            if ($currentTitle === '' || $currentTitle === 'Cùng THT Media tạo nên dấu ấn khác biệt') {
                $titles['vi'] = 'Cùng THT Media kể câu chuyện thương hiệu của bạn';
            }

            return $titles;
        });
    }

    private function mediaId(string $path): ?int
    {
        if (! Schema::hasTable('curator')) {
            return null;
        }

        $id = DB::table('curator')
            ->where('path', $path)
            ->where('type', 'like', 'image/%')
            ->value('id');

        return is_numeric($id) ? (int) $id : null;
    }
};
