<?php

namespace Database\Seeders;

use App\Settings\WebsiteSettings;
use Illuminate\Database\Seeder;

final class WebsiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = new WebsiteSettings([
            'site_name' => 'Tên doanh nghiệp',
            'tagline' => 'Giải pháp đồng bộ cho công trình',
            'company_name' => 'Tên doanh nghiệp',
            'contact_email' => 'hello@example.com',
            'hotline' => '0900 000 000',
            'contact_phone' => '0900 000 000',
            'address' => 'Việt Nam',
            'facebook_url' => '',
            'zalo_url' => '',
            'youtube_url' => '',
            'seo_title' => 'Tên doanh nghiệp | Giải pháp cho công trình',
            'seo_description' => 'Thông tin doanh nghiệp, dịch vụ, dự án và sản phẩm.',
            'seo_keywords' => 'doanh nghiệp, dịch vụ, dự án, sản phẩm',
            'logo_media_id' => null,
            'favicon_media_id' => null,
            'seo_image_media_id' => null,
            'company_profile_media_id' => null,
            'about_image_media_id' => null,
            'banner_media_id' => null,
            'header_menu_id' => null,
            'footer_menu_id' => null,
            'footer_background_media_id' => null,
            'google_maps_embed_url' => null,
            'google_maps_url' => null,
            'phones' => [],
            'branches' => [],
        ]);
        $settings->settingsConfig()->resetDefaultValueLoadedProperties();
        $settings->save();
    }
}
