<?php

namespace Database\Seeders;

use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

final class ProjectCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Công nghiệp', 'Thương mại', 'Dân dụng'] as $index => $name) {
            ProjectCategory::query()->updateOrCreate(['name' => $name], ['is_active' => true, 'sort_order' => ($index + 1) * 10]);
        }
    }
}
