<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

final class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['Tư vấn & khảo sát', 'Khảo sát hiện trạng và đề xuất phương án phù hợp.', 'Tư vấn, khảo sát và làm rõ nhu cầu thực tế trước khi triển khai.', 'hero'],
            ['Thiết kế & thi công', 'Triển khai giải pháp đồng bộ cho công trình.', 'Từ hồ sơ đến thi công, mỗi hạng mục được quản lý theo một quy trình rõ ràng.', 'engineering'],
            ['Bảo trì & hỗ trợ', 'Duy trì khả năng vận hành ổn định.', 'Kiểm tra định kỳ và hỗ trợ kỹ thuật khi doanh nghiệp cần.', 'technician'],
        ] as $index => [$categoryName, $title, $excerpt, $image]) {
            $category = ServiceCategory::query()->where('name', $categoryName)->firstOrFail();

            Service::query()->updateOrCreate(['title' => $title], [
                'service_category_id' => $category->getKey(), 'curator_media_id' => MediaSeeder::id($image),
                'excerpt' => $excerpt, 'body' => '<p>'.$excerpt.'</p>', 'status' => 'published',
                'is_home' => true, 'is_featured' => $index === 0, 'sort_order' => ($index + 1) * 10,
                'published_at' => now(), 'seo_title' => $title, 'seo_description' => $excerpt,
                'seo_image_media_id' => MediaSeeder::id($image),
            ]);
        }
    }
}
