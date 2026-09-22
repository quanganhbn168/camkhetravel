<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\PostCategory;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchPostRelationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MenuSeeder::class);
    }

    public function test_search_eager_loads_a_posts_single_category_relation(): void
    {
        $category = PostCategory::query()->create([
            'name' => 'Kiến thức kiểm thử',
            'is_active' => true,
        ]);
        $post = Post::query()->create([
            'post_category_id' => $category->getKey(),
            'title' => 'Bài viết tìm kiếm kiểm thử',
            'excerpt' => 'Nội dung tìm kiếm kiểm thử.',
            'body' => '<p>Nội dung tìm kiếm kiểm thử.</p>',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);

        $this->get('/tim-kiem?q='.urlencode('tìm kiếm kiểm thử'))
            ->assertOk()
            ->assertSee($post->title)
            ->assertViewHas('posts', function ($posts) use ($post, $category): bool {
                $result = $posts->firstWhere('id', $post->getKey());

                return $result?->relationLoaded('category')
                    && $result->relationLoaded('slugs')
                    && $result->category?->is($category);
            });
    }
}
