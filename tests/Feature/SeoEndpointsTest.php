<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoEndpointsTest extends TestCase
{
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

    public function test_frontend_master_always_allows_indexing(): void
    {
        $head = file_get_contents(resource_path('views/partials/head/seo.blade.php'));

        $this->assertIsString($head);
        $this->assertStringContainsString(
            '<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">',
            $head,
        );
    }
}
