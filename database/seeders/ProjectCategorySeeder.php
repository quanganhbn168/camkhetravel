<?php

namespace Database\Seeders;

use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

final class ProjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Công nghiệp', 'Thương mại', 'Dân dụng'] as $index => $name) {
            ProjectCategory::query()->updateOrCreate(['name' => $name], [
                'parent_id' => null, 'is_active' => true, 'sort_order' => ($index + 1) * 10,
                'curator_media_id' => MediaSeeder::id('facility'), 'banner_media_id' => MediaSeeder::id('warehouse'),
            ]);
        }
    }
}
