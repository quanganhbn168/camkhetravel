<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

final class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = ProductCategory::query()->where('name', 'Thiết bị báo cháy')->firstOrFail();
        $title = 'Thiết bị mẫu';
        $excerpt = 'Sản phẩm mẫu để bắt đầu xây dựng danh mục.';

        Product::query()->updateOrCreate(['sku' => 'SAMPLE-001'], [
            'product_category_id' => $category->getKey(), 'curator_media_id' => MediaSeeder::id('equipment'),
            'title' => $title, 'excerpt' => $excerpt, 'body' => '<p>'.$excerpt.'</p>', 'status' => 'published',
            'is_featured' => true, 'sort_order' => 10, 'published_at' => now(),
            'seo_title' => $title, 'seo_description' => $excerpt, 'seo_image_media_id' => MediaSeeder::id('equipment'),
        ]);
    }
}
