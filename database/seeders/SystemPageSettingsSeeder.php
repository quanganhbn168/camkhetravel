<?php

namespace Database\Seeders;

use App\Settings\SystemPageSettings;
use Illuminate\Database\Seeder;

final class SystemPageSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $ogImageId = MediaSeeder::id('no-image');

        if (! $ogImageId) {
            throw new \RuntimeException('Không thể seed hồ sơ trang hệ thống vì thiếu ảnh mặc định no-image.');
        }

        $settings = new SystemPageSettings([
            'home' => $this->profile('Trang chủ', 'CamKheTravel | Dịch vụ xe và hành trình du lịch', 'Dịch vụ xe du lịch, xe hợp đồng và tư vấn phương tiện theo lịch trình.', $ogImageId),
            'about' => $this->profile('Giới thiệu', 'Giới thiệu CamKheTravel', 'Thông tin về CamKheTravel và các dịch vụ hỗ trợ hành trình.', $ogImageId),
            'services' => $this->profile('Dịch vụ', 'Dịch vụ xe | CamKheTravel', 'Các dịch vụ xe du lịch, xe hợp đồng, xe ghép và xe cưới.', $ogImageId),
            'solutions' => $this->profile('Giải pháp', 'Giải pháp hành trình | CamKheTravel', 'Tư vấn phương tiện và lịch trình theo nhu cầu chuyến đi.', $ogImageId),
            'contact' => $this->profile('Liên hệ', 'Liên hệ CamKheTravel', 'Gửi lịch trình để CamKheTravel tiếp nhận yêu cầu tư vấn.', $ogImageId),
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
