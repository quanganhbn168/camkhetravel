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
            'page_intro' => 'Đơn vị đồng hành trong tư vấn, thiết kế, thi công và bảo trì hệ thống PCCC.',
            'story_title' => 'Năng lực được xây dựng từ thực tế',
            'story' => '<p>DVTEC tập trung vào giải pháp phù hợp hiện trạng, tiêu chuẩn kỹ thuật và nhu cầu vận hành của từng công trình.</p>',
            'story_image_media_id' => null,
            'video_source' => '',
            'video_youtube_url' => '',
            'video_media_id' => null,
            'video_poster_media_id' => null,
            'history' => '',
            'history_title' => 'Hành trình phát triển',
            'history_description' => 'Các dấu mốc được cập nhật tại trang quản trị.',
            'history_timeline' => [],
            'mission' => 'Mang đến giải pháp PCCC phù hợp và có khả năng vận hành bền vững.',
            'vision' => 'Trở thành đối tác kỹ thuật tin cậy của doanh nghiệp và chủ đầu tư.',
            'core_values' => '<p>Trách nhiệm, minh bạch và đồng hành dài hạn.</p>',
            'core_values_image_media_id' => null,
            'principles_title' => 'Nguyên tắc làm việc',
            'services_title' => 'Dịch vụ của DVTEC',
            'services_link_label' => 'Xem tất cả dịch vụ',
            'stats_title' => 'Năng lực triển khai',
            'office_title' => 'Văn phòng DVTEC',
            'office_description' => 'Không gian làm việc và phối hợp dự án.',
            'office_image_media_id' => null,
            'office_gallery' => [],
            'cta_title' => 'Trao đổi nhu cầu PCCC của công trình',
            'cta_button_label' => 'Liên hệ DVTEC',
        ]);
        $settings->settingsConfig()->resetDefaultValueLoadedProperties();
        $settings->save();
        $this->call(AboutProfileContentSeeder::class);
    }
}
