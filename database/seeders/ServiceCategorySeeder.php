<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

final class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['Dịch vụ xe và du lịch', 'Dịch vụ phương tiện cho hành trình cá nhân, gia đình, doanh nghiệp và đối tác lữ hành.'],
        ] as $index => [$name, $description]) {
            ServiceCategory::query()->updateOrCreate(['name' => $name], [
                'description' => $description, 'is_active' => true, 'is_featured' => true, 'is_home' => true,
                'sort_order' => ($index + 1) * 10,
                'parent_id' => null,
                'curator_media_id' => MediaSeeder::id('no-image'),
                'banner_media_id' => MediaSeeder::id('no-image'),
            ]);
        }
    }
}
