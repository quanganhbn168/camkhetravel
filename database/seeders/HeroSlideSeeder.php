<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

final class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        HeroSlide::query()->updateOrCreate(['sort_order' => 10], [
            'curator_media_id' => MediaSeeder::id('no-image'),
            'title' => 'Bao xe Phú Thọ',
            'description' => 'Đi tỉnh · Du lịch gia đình · Công tác · Xe cưới',
            'primary_label' => 'Nhận báo giá chuyến đi', 'primary_url' => '#bao-gia',
            'secondary_label' => 'Xem dịch vụ', 'secondary_url' => '#dich-vu', 'is_active' => true,
        ]);
    }
}
