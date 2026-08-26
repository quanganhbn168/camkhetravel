<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Support\Localization\LocalizedUrl;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostCanonicalUrlTest extends TestCase
{
    use DatabaseTransactions;

    public function test_post_detail_uses_the_root_slug_and_legacy_news_url_redirects_to_it(): void
    {
        $post = Post::query()->published()->firstOrFail();
        $canonicalUrl = LocalizedUrl::post($post);
        $legacyUrl = LocalizedUrl::route('posts.show', ['slug' => $post->legacyContent?->slug ?: $post->slug]);

        $this->assertSame(url('/'.($post->legacyContent?->slug ?: $post->slug)), $canonicalUrl);

        $this->get($canonicalUrl)
            ->assertOk()
            ->assertSee($post->title);

        $this->get($legacyUrl)
            ->assertMovedPermanently()
            ->assertRedirect($canonicalUrl);
    }
}
