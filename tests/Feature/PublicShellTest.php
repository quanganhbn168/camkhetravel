<?php

namespace Tests\Feature;

use Database\Seeders\MenuSeeder;
use Database\Seeders\WebsiteSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicShellTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_uses_the_shared_bootstrap_navigation(): void
    {
        $this->seed(WebsiteSettingsSeeder::class);
        $this->seed(MenuSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('class="header-sticky" data-site-header', false)
            ->assertSee('data-bs-toggle="offcanvas"', false)
            ->assertSee('id="mobile-drawer"', false)
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

    public function test_floating_contact_and_scroll_controls_have_usable_styles(): void
    {
        $styles = file_get_contents(resource_path('css/components/floating-actions.css'));
        $foundation = file_get_contents(resource_path('css/theme.css'));
        $frontendStyles = file_get_contents(resource_path('css/frontend.css'));

        $this->assertStringContainsString('--site-primary: #07543f;', $foundation);
        $this->assertStringContainsString('--bs-primary: var(--site-primary);', $foundation);
        $this->assertStringNotContainsString('brand.overrides', $frontendStyles);
        $this->assertStringContainsString("@import './components/floating-actions.css';", $frontendStyles);
        $this->assertStringContainsString('.floating-action {', $styles);
        $this->assertStringContainsString('.floating-action--phone::before', $styles);
        $this->assertStringContainsString('@keyframes phone-ring', $styles);
        $this->assertStringContainsString('.scroll-top.is-visible', $styles);
    }
}
