<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use Illuminate\Database\Seeder;

final class PostSeeder extends Seeder
{
    public function run(): void
    {
        $title = 'Bài viết mẫu';
        $excerpt = 'Bài viết mẫu để bắt đầu xây dựng chuyên mục kiến thức.';
        $post = Post::query()->updateOrCreate(['title' => $title], [
            'curator_media_id' => MediaSeeder::id('warehouse'), 'excerpt' => $excerpt,
            'body' => '<p>'.$excerpt.'</p>', 'status' => 'published', 'is_featured' => true,
            'published_at' => now(), 'seo_title' => $title, 'seo_description' => $excerpt,
            'seo_image_media_id' => MediaSeeder::id('warehouse'),
        ]);

        $category = PostCategory::query()->where('name', 'Kiến thức')->firstOrFail();
        $tag = Tag::query()->where('name', 'Giải pháp')->firstOrFail();
        $post->categories()->sync([$category->getKey() => ['sort_order' => 10]]);
        $post->tags()->sync([$tag->getKey() => ['sort_order' => 10]]);
    }
}
