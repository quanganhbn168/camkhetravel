<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

final class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        $slide = HeroSlide::query()->updateOrCreate(['sort_order' => 10], [
            'curator_media_id' => MediaSeeder::id('hero'),
            'title' => 'Giải pháp phù hợp cho từng công trình',
            'description' => 'Cập nhật thông điệp và hình ảnh tại khu vực quản trị.',
            'primary_label' => 'Liên hệ tư vấn', 'primary_url' => '/lien-he',
            'secondary_label' => 'Xem dịch vụ', 'secondary_url' => '/dich-vu', 'is_active' => true,
        ]);

    }
}
