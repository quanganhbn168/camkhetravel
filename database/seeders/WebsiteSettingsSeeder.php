<?php

namespace Database\Seeders;

use App\Settings\WebsiteSettings;
use Illuminate\Database\Seeder;

final class WebsiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = new WebsiteSettings([
            'site_name' => 'CamKheTravel',
            'tagline' => 'Đồng hành cùng những hành trình đáng nhớ',
            'company_name' => 'CamKheTravel',
            'contact_email' => '',
            'hotline' => '0354865688',
            'contact_phone' => '0354865688',
            'address' => '',
            'facebook_url' => '',
            'zalo_url' => 'https://zalo.me/0354865688',
            'youtube_url' => '',
            'seo_title' => 'CamKheTravel | Dịch vụ xe và hành trình du lịch',
            'seo_description' => 'Dịch vụ xe du lịch, xe hợp đồng và tư vấn phương tiện theo lịch trình.',
            'seo_keywords' => 'CamKheTravel, xe du lịch, xe hợp đồng, hành trình du lịch',
            'logo_media_id' => null,
            'seo_image_media_id' => null,
            'company_profile_media_id' => null,
            'about_image_media_id' => null,
            'banner_media_id' => null,
            'header_menu_id' => null,
            'footer_menu_id' => null,
            'footer_background_media_id' => null,
            'google_maps_embed_url' => null,
            'phones' => [['label' => 'Hotline', 'number' => '0354865688', 'is_primary' => true]],
            'branches' => [],
        ]);
        $settings->settingsConfig()->resetDefaultValueLoadedProperties();
        $settings->save();
    }
}
