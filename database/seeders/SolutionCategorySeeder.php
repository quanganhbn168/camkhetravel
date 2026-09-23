<?php

namespace Database\Seeders;

use App\Models\SolutionCategory;
use Illuminate\Database\Seeder;

final class SolutionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['pccc', 'PCCC', 'giai-phap-pccc'],
            ['hvac', 'HVAC & Thông gió', 'giai-phap-hvac-thong-gio'],
            ['industrial-electrical', 'Điện công nghiệp', 'giai-phap-dien-cong-nghiep'],
            ['elv-camera', 'Điện nhẹ & Camera', 'giai-phap-dien-nhe-camera'],
            ['infrastructure', 'Hạ tầng kỹ thuật', 'giai-phap-ha-tang-ky-thuat'],
            ['mechanical', 'Cơ khí, trần & vách', 'giai-phap-co-khi-tran-vach'],
        ];

        foreach ($categories as $index => [$key, $name, $slug]) {
            // A stable internal key prevents repeat seeds from overwriting editor changes.
            SolutionCategory::firstOrCreate(['seed_key' => $key], [
                'name' => $name,
                // Slugs share a global registry with services, posts and products.
                'slug' => $slug,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
