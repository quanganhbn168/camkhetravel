<?php

namespace Database\Seeders;

use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

final class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Thiết bị báo cháy', 'Thiết bị chữa cháy', 'Phụ kiện hệ thống'] as $index => $name) {
            ProductCategory::query()->updateOrCreate(['name' => $name], ['is_active' => true, 'sort_order' => ($index + 1) * 10]);
        }
    }
}
