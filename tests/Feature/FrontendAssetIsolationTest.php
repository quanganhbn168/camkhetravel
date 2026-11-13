<?php

namespace Tests\Feature;

use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendAssetIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_with_the_frontend_entry_and_not_the_filament_theme(): void
    {
        $this->seed(MenuSeeder::class);
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
        $frontend = $manifest['resources/css/frontend.css']['file'];
        $admin = $manifest['resources/css/filament/admin/theme.css']['file'];
        foreach (['/', '/gioi-thieu', '/dich-vu', '/giai-phap', '/san-pham', '/blog', '/lien-he', '/tim-kiem?q=pccc'] as $url) {
            $this->get($url)->assertOk()->assertSee($frontend, false)->assertDontSee($admin, false);
        }
    }

    public function test_slider_and_scroll_reveal_load_only_on_pages_that_call_them(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
        $serviceScript = $manifest['resources/js/pages/service.js']['file'];
        $serviceStyles = $manifest['resources/js/pages/service.js']['css'][0];
        $service = \App\Models\Service::query()->published()->firstOrFail();

        $this->get('/'.$service->slug)->assertOk()->assertSee($serviceScript, false)->assertSee($serviceStyles, false);

        foreach (['/', '/gioi-thieu', '/dich-vu', '/blog', '/lien-he'] as $url) {
            $this->get($url)->assertOk()->assertDontSee($serviceScript, false)->assertDontSee($serviceStyles, false);
        }
    }

    public function test_home_uses_shared_header_and_footer(): void
    {
        $this->seed(MenuSeeder::class);
        $this->get('/')->assertOk()
            ->assertSee('class="header-sticky" data-site-header', false)
            ->assertSee('data-bs-target="#mobile-drawer"', false)
            ->assertSee('<footer class="footer">', false)
            ->assertDontSee('id="mainNav"', false);
    }
}
