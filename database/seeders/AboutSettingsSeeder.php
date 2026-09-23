<?php

namespace Database\Seeders;

use App\Settings\AboutSettings;
use Illuminate\Database\Seeder;

final class AboutSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = new AboutSettings([
            'default_image_media_id' => null,
            'page_stats' => [],
            'page_intro' => 'CamKheTravel cung cấp dịch vụ xe và hỗ trợ phương tiện theo hành trình của khách hàng.',
            'story_title' => 'Đồng hành cùng những hành trình',
            'story' => '<p>CamKheTravel tiếp nhận lịch trình, tư vấn phương án xe và trao đổi các thông tin cần thiết trước chuyến đi.</p>',
            'story_image_media_id' => null,
            'video_source' => '',
            'video_youtube_url' => '',
            'video_media_id' => null,
            'video_poster_media_id' => null,
            'history' => '',
            'history_title' => 'Thông tin CamKheTravel',
            'history_description' => '',
            'history_timeline' => [],
            'mission' => 'Hỗ trợ khách hàng lựa chọn phương tiện phù hợp với lịch trình đã trao đổi.',
            'vision' => 'Đồng hành cùng khách hàng và đối tác trong những hành trình thuận tiện.',
            'core_values' => '<p>Rõ ràng trong trao đổi, chủ động trong lịch trình và tận tâm khi hỗ trợ.</p>',
            'core_values_image_media_id' => null,
            'principles_title' => 'Nguyên tắc làm việc',
            'services_title' => 'Dịch vụ của CamKheTravel',
            'services_link_label' => 'Xem tất cả dịch vụ',
            'stats_title' => 'Năng lực triển khai',
            'office_title' => 'Liên hệ CamKheTravel',
            'office_description' => '',
            'office_image_media_id' => null,
            'office_gallery' => [],
            'cta_title' => 'Trao đổi về hành trình sắp tới',
            'cta_button_label' => 'Liên hệ CamKheTravel',
        ]);
        $settings->settingsConfig()->resetDefaultValueLoadedProperties();
        $settings->save();
        $this->call(AboutProfileContentSeeder::class);
    }
}
