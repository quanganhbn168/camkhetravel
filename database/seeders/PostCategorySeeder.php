<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Seeder;

final class PostCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Kiến thức', 'Tin tức'] as $index => $name) {
            PostCategory::query()->updateOrCreate(['name' => $name], [
                'parent_id' => null, 'is_active' => true, 'sort_order' => ($index + 1) * 10,
                'curator_media_id' => MediaSeeder::id('technician'), 'banner_media_id' => MediaSeeder::id('warehouse'),
            ]);
        }
    }
}
