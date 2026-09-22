<?php

namespace Tests\Feature;

use App\Models\Post;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCanonicalUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MenuSeeder::class);
    }

    public function test_posts_listing_uses_blog_path_and_legacy_listing_redirects(): void
    {
        $blogUrl = route('posts.index');

        $this->assertSame(url('/blog'), $blogUrl);

        $this->get($blogUrl)
            ->assertOk();

        $this->get('/tin-tuc')
            ->assertMovedPermanently()
            ->assertRedirect($blogUrl);
    }

    public function test_post_detail_uses_the_root_slug_and_news_listing_url_redirects_to_it(): void
    {
        $post = Post::query()->create([
            'title' => 'Bài viết kiểm thử',
            'slug' => 'bai-viet-kiem-thu',
            'excerpt' => 'Mô tả bài viết kiểm thử.',
            'body' => '<p>Nội dung bài viết kiểm thử.</p>',
            'status' => 'published',
            'published_at' => now()->subMinute(),
        ]);
        $canonicalUrl = route('slug.show', ['slug' => $post->slug]);
        $listingUrl = route('posts.show', ['slug' => $post->slug]);

        $this->assertSame(url('/'.$post->slug), $canonicalUrl);

        $this->get($canonicalUrl)
            ->assertOk()
            ->assertSee($post->title);

        $this->get($listingUrl)
            ->assertMovedPermanently()
            ->assertRedirect($canonicalUrl);
    }
}
