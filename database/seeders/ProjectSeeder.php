<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

final class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $category = ProjectCategory::query()->where('name', 'Công nghiệp')->firstOrFail();
        $title = 'Dự án công trình mẫu';
        $excerpt = 'Hồ sơ dự án mẫu để bắt đầu quản trị nội dung.';

        Project::query()->updateOrCreate(['title' => $title], [
            'project_category_id' => $category->getKey(), 'curator_media_id' => MediaSeeder::id('facility'),
            'excerpt' => $excerpt, 'body' => '<p>'.$excerpt.'</p>', 'status' => 'published', 'is_featured' => true,
            'sort_order' => 10, 'published_at' => now(), 'completed_at' => now()->toDateString(),
            'seo_title' => $title, 'seo_description' => $excerpt, 'seo_image_media_id' => MediaSeeder::id('facility'),
        ]);
    }
}
