<?php

namespace Tests\Feature;

use App\Settings\AboutSettings;
use Database\Seeders\WebsiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendAssetIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_with_the_frontend_entry_and_not_the_filament_theme(): void
    {
        $this->seed(WebsiteSeeder::class);
        $about = app(AboutSettings::class);
        $about->page_title = ['vi' => 'Giới thiệu kiểm thử'];
        $about->save();
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
        $frontend = $manifest['resources/scss/frontend.scss']['file'];
        $admin = $manifest['resources/css/filament/admin/theme.css']['file'];
        foreach (['/', '/gioi-thieu', '/dich-vu', '/du-an', '/san-pham', '/blog', '/bang-gia', '/lien-he', '/tim-kiem?q=pccc'] as $url) {
            $this->get($url)->assertOk()->assertSee($frontend, false)->assertDontSee($admin, false);
        }
    }

    public function test_header_exposes_bootstrap_accessible_dialogs(): void
    {
        $this->seed(WebsiteSeeder::class);
        $this->get('/')->assertOk()->assertSee('id="mobile-drawer"', false)
            ->assertSee('id="header-search-modal"', false)
            ->assertSee('data-bs-toggle="offcanvas"', false)
            ->assertDontSee('syncBodyLock', false);
    }
}
