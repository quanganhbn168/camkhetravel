<?php

namespace Tests\Feature;

use Database\Seeders\WebsiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteSeeder::class);
    }

    public function test_robots_uses_app_url_and_allows_public_crawling(): void
    {
        config(['app.url' => 'https://demo.example']);

        $this->withServerVariables([
            'HTTP_HOST' => 'request-host.example',
            'HTTPS' => 'off',
        ])->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertContent(implode(PHP_EOL, [
                'User-agent: *',
                'Allow: /',
                '',
                'Sitemap: https://demo.example/sitemap.xml',
                '',
            ]));
    }

    public function test_frontend_pages_are_indexable_and_sitemap_is_native(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">', false);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<urlset', false)
            ->assertDontSee('data-event-', false);
    }
}
