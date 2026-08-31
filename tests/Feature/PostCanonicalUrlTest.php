<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Support\Localization\LocalizedUrl;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostCanonicalUrlTest extends TestCase
{
    use DatabaseTransactions;

    public function test_post_detail_uses_the_root_slug_and_news_listing_url_redirects_to_it(): void
    {
        $post = Post::query()->published()->firstOrFail();
        $canonicalUrl = LocalizedUrl::post($post);
        $listingUrl = LocalizedUrl::route('posts.show', ['slug' => $post->slug]);

        $this->assertSame(url('/'.$post->slug), $canonicalUrl);

        $this->get($canonicalUrl)
            ->assertOk()
            ->assertSee($post->title);

        $this->get($listingUrl)
            ->assertMovedPermanently()
            ->assertRedirect($canonicalUrl);
    }
}
