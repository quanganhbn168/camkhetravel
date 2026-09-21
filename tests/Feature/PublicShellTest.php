<?php

namespace Tests\Feature;

use Database\Seeders\WebsiteSeeder;
use Database\Seeders\WebsiteSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicShellTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_uses_bootstrap_navigation_without_alpine_markup(): void
    {
        $this->seed(WebsiteSettingsSeeder::class);
        $this->seed(WebsiteSeeder::class);

        $this->get('/')
            ->assertOk()
            ->assertSee('--primary-color: #e52327;', false)
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

    public function test_floating_contact_and_scroll_controls_have_usable_styles(): void
    {
        $styles = file_get_contents(resource_path('css/frontend/site.css'));
        $tokens = file_get_contents(resource_path('views/components/site-design-tokens.blade.php'));

        $this->assertStringContainsString('--primary-color:', $tokens);
        $this->assertStringContainsString('--bs-primary: var(--primary-color);', $tokens);
        $this->assertStringContainsString('.floating-action {', $styles);
        $this->assertStringContainsString('.floating-action--phone::before', $styles);
        $this->assertStringContainsString('rgb(var(--primary-color-rgb) / 0.72)', $styles);
        $this->assertStringContainsString('@keyframes phone-ring', $styles);
        $this->assertStringContainsString('.scroll-top.is-visible', $styles);
    }
}
