<?php

namespace Database\Seeders;

use App\Settings\CompanySettings;
use App\Settings\DesignSettings;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use Illuminate\Database\Seeder;

final class WebsiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $website = app(WebsiteSettings::class);
        $website->site_name = 'Tên doanh nghiệp';
        $website->tagline = 'Giải pháp đồng bộ cho công trình';
        $website->company_name = 'Tên doanh nghiệp';
        $website->contact_email = 'hello@example.com';
        $website->hotline = '0900 000 000';
        $website->contact_phone = '0900 000 000';
        $website->address = 'Việt Nam';
        $website->save();

        $homepage = app(HomepageSettings::class);
        $homepage->about_eyebrow = ['vi' => 'VỀ CHÚNG TÔI'];
        $homepage->about_title = ['vi' => 'Giải pháp phù hợp cho từng công trình'];
        $homepage->about_content = ['vi' => 'Cập nhật nội dung giới thiệu doanh nghiệp tại trang quản trị.'];
        $homepage->save();

        $company = app(CompanySettings::class);
        $company->founded_year = now()->year;
        $company->save();

        $design = app(DesignSettings::class);
        $design->color_primary = '#0d6efd';
        $design->color_primary_hover = '#0b5ed7';
        $design->color_ink = '#212529';
        $design->color_surface = '#f8f9fa';
        $design->color_muted = '#e9ecef';
        $design->save();
    }
}
