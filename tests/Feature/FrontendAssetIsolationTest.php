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
        foreach (['/', '/gioi-thieu', '/dich-vu', '/giai-phap', '/du-an', '/san-pham', '/blog', '/lien-he', '/tim-kiem?q=pccc'] as $url) {
            $this->get($url)->assertOk()->assertSee($frontend, false)->assertDontSee($admin, false);
        }
    }

    public function test_home_header_exposes_bootstrap_accessible_navigation(): void
    {
        $this->seed(MenuSeeder::class);
        $this->get('/')->assertOk()->assertSee('id="mainNav"', false)
            ->assertSee('data-bs-toggle="collapse"', false)
            ->assertSee('aria-controls="mainNav"', false)
            ->assertDontSee('syncBodyLock', false);
    }
}
