<?php

namespace Database\Seeders;

use App\Settings\SystemPageSettings;
use Illuminate\Database\Seeder;

final class SystemPageSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $ogImageId = MediaSeeder::id('engineering-team');

        if (! $ogImageId) {
            throw new \RuntimeException('Không thể seed hồ sơ trang hệ thống vì thiếu media engineering-team.');
        }

        $settings = new SystemPageSettings([
            'home' => $this->profile('Trang chủ', 'DVTEC | Giải pháp PCCC đồng bộ', 'Tư vấn, thiết kế, thi công và bảo trì hệ thống phòng cháy chữa cháy.', $ogImageId),
            'about' => $this->profile('Giới thiệu', 'Giới thiệu DVTEC', 'Thông tin năng lực, định hướng và quá trình phát triển của DVTEC.', $ogImageId),
            'services' => $this->profile('Dịch vụ', 'Dịch vụ PCCC | DVTEC', 'Các dịch vụ tư vấn, thiết kế, thi công và bảo trì hệ thống PCCC.', $ogImageId),
            'solutions' => $this->profile('Giải pháp', 'Giải pháp PCCC | DVTEC', 'Các giải pháp PCCC được DVTEC xây dựng cho từng nhu cầu công trình.', $ogImageId),
            'contact' => $this->profile('Liên hệ', 'Liên hệ DVTEC', 'Liên hệ DVTEC để được tư vấn phương án PCCC phù hợp.', $ogImageId),
            'projects' => $this->profile('Dự án', 'Dự án PCCC | DVTEC', 'Các công trình PCCC tiêu biểu do DVTEC triển khai.', $ogImageId),
        ]);
        $settings->settingsConfig()->resetDefaultValueLoadedProperties();
        $settings->save();
    }

    /** @return array<string, mixed> */
    private function profile(string $title, string $seoTitle, string $seoDescription, int $ogImageId): array
    {
        return [
            'title' => $title,
            'seo_title' => $seoTitle,
            'seo_description' => $seoDescription,
            'og_image_media_id' => $ogImageId,
            'banner_media_id' => null,
        ];
    }
}
