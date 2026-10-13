<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Settings\CrawlSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrawlSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_follows_the_saved_settings(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Allow: /', false)
            ->assertSee('Sitemap:', false);

        $crawl = app(CrawlSettings::class);
        $crawl->robots_disallow = "admin\n/tim-kiem";
        $crawl->save();

        $this->get('/robots.txt')
            ->assertSee('Disallow: /admin', false)
            ->assertSee('Disallow: /tim-kiem', false);

        $crawl->robots_block_all = true;
        $crawl->save();

        $this->get('/robots.txt')
            ->assertSee('Disallow: /', false)
            ->assertDontSee('Sitemap:', false);
    }

    public function test_sitemap_only_lists_enabled_sections(): void
    {
        Page::query()->create(['title' => 'Chính sách', 'slug' => 'chinh-sach', 'status' => 'published']);

        $this->get('/sitemap.xml')->assertSee('/chinh-sach', false);

        $crawl = app(CrawlSettings::class);
        $crawl->sitemap_sections = ['pages'];
        $crawl->save();

        $this->get('/sitemap.xml')
            ->assertDontSee('/chinh-sach', false)
            ->assertSee(route('contact'), false);
    }
}
