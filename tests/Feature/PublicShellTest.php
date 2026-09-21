<?php

namespace Tests\Feature;

use Database\Seeders\WebsiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicShellTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_uses_bootstrap_navigation_without_alpine_markup(): void
    {
        $this->seed(WebsiteSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('site-header__toggle', false)
            ->assertSee('offcanvas', false)
            ->assertSee('data-bs-toggle="modal"', false)
            ->assertDontSee('x-data', false);
    }

    public function test_public_runtime_uses_bootstrap_and_swiper_without_legacy_ui_libraries(): void
    {
        $runtime = file_get_contents(resource_path('js/app.js'));
        $sliders = file_get_contents(resource_path('js/frontend/sliders.js'));

        $this->assertStringContainsString("from 'bootstrap/js/dist/collapse'", $runtime);
        $this->assertStringContainsString("from 'swiper'", $sliders);
        $this->assertStringNotContainsString('alpinejs', $runtime);
        $this->assertStringNotContainsString('glightbox', $runtime);
        $this->assertStringNotContainsString('sweetalert2', $runtime);
        $this->assertStringNotContainsString('IntersectionObserver', $runtime);
    }
}
