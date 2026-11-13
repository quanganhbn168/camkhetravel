<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSchemaTest extends TestCase
{
    use RefreshDatabase;

    /** @return list<string> */
    private function schemaTypes(string $url): array
    {
        $html = $this->get($url)->assertOk()->getContent();
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);
        // Tất cả schema của trang nằm chung trong @graph của một thẻ script; trang cần bao nhiêu schema thì @graph có bấy nhiêu node.
        $this->assertLessThanOrEqual(1, count($matches[1]));

        $types = [];
        foreach ($matches[1] as $json) {
            $data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
            $this->assertSame('https://schema.org', $data['@context']);
            foreach ($data['@graph'] as $node) {
                $types[] = $node['@type'];
            }
        }

        return $types;
    }

    public function test_each_page_outputs_only_its_own_schema(): void
    {
        $this->seed(DatabaseSeeder::class);
        $post = Post::query()->published()->firstOrFail();
        $service = Service::query()->published()->firstOrFail();

        $this->assertSame(['WebSite', 'WebPage', 'FAQPage'], array_slice($this->schemaTypes('/'), 1));
        $this->assertContains($this->schemaTypes('/')[0], ['LocalBusiness', 'Organization']);
        $this->assertSame(['BlogPosting', 'BreadcrumbList'], $this->schemaTypes('/'.$post->slug));
        $this->assertSame(['CollectionPage', 'BreadcrumbList'], $this->schemaTypes(route('posts.index')));
        $this->assertSame('Service', $this->schemaTypes('/'.$service->slug)[0]);
        $this->assertSame(['ContactPage', 'BreadcrumbList'], $this->schemaTypes(route('contact')));
        $this->assertSame(['AboutPage', 'BreadcrumbList'], $this->schemaTypes(route('about')));
        $this->assertSame([], $this->schemaTypes(route('search', ['q' => 'xe'])));
    }

    public function test_main_pages_have_exactly_one_h1_and_no_heading_before_it(): void
    {
        $this->seed(DatabaseSeeder::class);
        $post = Post::query()->published()->firstOrFail();
        $service = Service::query()->published()->firstOrFail();

        foreach (['/', '/'.$post->slug, '/'.$service->slug, route('posts.index'), route('services.index')] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertSame(1, preg_match_all('#<h1[\s>]#', $html), $url.' phải có đúng 1 H1.');
            $this->assertSame(0, preg_match('#<h[2-6][\s>]#', strstr($html, '<h1', true)), $url.' có heading nằm trước H1.');
        }
    }

    public function test_home_is_local_business_when_address_is_set(): void
    {
        $this->seed(DatabaseSeeder::class);
        $website = app(\App\Settings\WebsiteSettings::class);
        $website->address = 'Xã Cẩm Khê, Phú Thọ';
        $website->save();

        $this->assertSame('LocalBusiness', $this->schemaTypes('/')[0]);
    }

    public function test_service_rating_comes_only_from_approved_reviews(): void
    {
        $this->seed(DatabaseSeeder::class);
        $service = Service::query()->published()->firstOrFail();
        $url = '/'.$service->slug;

        $this->assertStringNotContainsString('aggregateRating', $this->get($url)->getContent());

        $service->comments()->create(['author_name' => 'Anh Hùng', 'body' => 'Tài xế đến đúng giờ.', 'rating' => 5, 'status' => 'approved']);
        $service->comments()->create(['author_name' => 'Chị Lan', 'body' => 'Xe sạch.', 'rating' => 4, 'status' => 'approved']);
        $service->comments()->create(['author_name' => 'Spam', 'body' => 'x', 'rating' => 1, 'status' => 'pending']);

        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $this->get($url)->getContent(), $match);
        $node = json_decode($match[1], true)['@graph'][0];

        $this->assertSame('Service', $node['@type']);
        $this->assertEquals(4.5, $node['aggregateRating']['ratingValue']);
        $this->assertSame(2, $node['aggregateRating']['ratingCount']);
        $this->assertCount(2, $node['review']);
    }

    public function test_post_has_article_meta_and_inline_publisher(): void
    {
        $this->seed(DatabaseSeeder::class);
        $post = Post::query()->published()->firstOrFail();

        $html = $this->get('/'.$post->slug)->assertOk()
            ->assertSee('property="article:published_time"', false)
            ->assertSee('property="article:modified_time"', false)
            ->getContent();
        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $match);
        $article = json_decode($match[1], true)['@graph'][0];

        $this->assertSame('Organization', $article['publisher']['@type']);
        $this->assertNotSame('', $article['publisher']['name']);
        $this->assertArrayNotHasKey('@id', $article['publisher']);
    }
}
