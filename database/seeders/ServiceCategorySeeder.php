<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

final class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['Tư vấn & khảo sát', 'Khảo sát và xác định nhu cầu cho công trình.'],
            ['Thiết kế & thi công', 'Triển khai giải pháp theo thực tế công trình.'],
            ['Bảo trì & hỗ trợ', 'Đồng hành trong quá trình vận hành.'],
        ] as $index => [$name, $description]) {
            ServiceCategory::query()->updateOrCreate(['name' => $name], [
                'description' => $description, 'is_active' => true, 'is_featured' => true, 'is_home' => true,
                'sort_order' => ($index + 1) * 10,
                'parent_id' => null,
                'curator_media_id' => MediaSeeder::id(['installation-team', 'engineering-team', 'technician'][$index]),
                'banner_media_id' => MediaSeeder::id('facility'),
            ]);
        }
    }
}
