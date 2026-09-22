<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

final class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Thiết bị báo cháy', 'Thiết bị chữa cháy', 'Phụ kiện hệ thống'] as $index => $name) {
            ProductCategory::query()->updateOrCreate(['name' => $name], [
                'parent_id' => null, 'is_active' => true, 'sort_order' => ($index + 1) * 10,
                'curator_media_id' => MediaSeeder::id('equipment'), 'banner_media_id' => MediaSeeder::id('facility'),
            ]);
        }
        $parent = ProductCategory::query()->where('name', 'Phụ kiện hệ thống')->firstOrFail();
        ProductCategory::query()->updateOrCreate(['name' => 'Phụ kiện lắp đặt'], [
            'parent_id' => $parent->id, 'is_active' => true, 'sort_order' => 10,
            'curator_media_id' => MediaSeeder::id('equipment'), 'banner_media_id' => MediaSeeder::id('facility'),
        ]);
    }
}
