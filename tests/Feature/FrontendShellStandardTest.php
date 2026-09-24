<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontendShellStandardTest extends TestCase
{
    public function test_frontend_uses_a_single_button_scale_and_a_non_sticky_topbar(): void
    {
        $foundation = file_get_contents(resource_path('css/reset.css'));
        $brand = file_get_contents(resource_path('css/frontend.css'));
        $runtime = file_get_contents(resource_path('js/app.js'));
        $header = file_get_contents(resource_path('views/partials/header.blade.php'));
        $actions = file_get_contents(resource_path('views/partials/header/actions.blade.php'));
        $headerStyles = file_get_contents(resource_path('css/components/header.css'));
        $entrypoint = file_get_contents(resource_path('css/frontend.css'));
        $homeStyles = file_get_contents(resource_path('css/pages/home.css'));
        $home = file_get_contents(resource_path('views/frontend/home.blade.php'));

        $this->assertStringContainsString('font-size: 16px;', $foundation);
        $this->assertStringNotContainsString('.btn {', $brand);
        $this->assertStringContainsString('text-decoration-thickness: 2px;', $headerStyles);
        $this->assertStringContainsString('<div class="topbar">', $header);
        $this->assertStringContainsString('<header class="header-sticky" data-site-header', $header);
        $this->assertStringNotContainsString('Hotline:', $header);
        $this->assertStringNotContainsString('header__phones', $actions);
        $this->assertStringNotContainsString('button-primary', $home);
        $this->assertStringNotContainsString('button-secondary', $home);
        $this->assertStringNotContainsString('header__phones', $headerStyles);
        $this->assertStringNotContainsString('@use', $entrypoint);
        $this->assertStringContainsString('.header-sticky.is-scroll-hidden:not(:focus-within)', $headerStyles);
        $this->assertStringContainsString("header.classList.toggle('is-scroll-hidden', currentScroll > previousScroll)", $runtime);
        $this->assertStringNotContainsString('final-cta', $home);
        $this->assertStringNotContainsString('final-cta', $homeStyles);
        $this->assertStringContainsString('CamKheTravel', $home);
        $this->assertStringContainsString('Mỗi hành trình', $home);
    }

    public function test_shared_header_and_home_hero_each_have_one_style_owner(): void
    {
        $headerStyles = file_get_contents(resource_path('css/components/header.css'));
        $homeStyles = file_get_contents(resource_path('css/pages/home.css'));
        $entrypoint = file_get_contents(resource_path('css/frontend.css'));
        $views = implode("\n", array_map(
            static fn (\SplFileInfo $file): string => file_get_contents($file->getPathname()),
            iterator_to_array(new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS)
            ))
        ));

        $this->assertStringContainsString('.header-sticky', $headerStyles);
        $this->assertStringContainsString('.topbar', $headerStyles);
        $this->assertStringContainsString('.hero', $homeStyles);
        $this->assertStringNotContainsString('@use', $entrypoint);
        $this->assertStringNotContainsString('button-dark', $views);
    }

    public function test_bootstrap_css_is_loaded_before_website_layers(): void
    {
        $entrypoint = file_get_contents(resource_path('css/frontend.css'));

        $bootstrapPosition = strpos($entrypoint, "@import 'bootstrap/dist/css/bootstrap.min.css';");
        $themePosition = strpos($entrypoint, "@import './theme.css';");
        $headerPosition = strpos($entrypoint, "@import './components/header.css';");

        $this->assertNotFalse($bootstrapPosition);
        $this->assertNotFalse($themePosition);
        $this->assertNotFalse($headerPosition);
        $this->assertLessThan($themePosition, $bootstrapPosition);
        $this->assertLessThan($headerPosition, $themePosition);
        $this->assertLessThan($headerPosition, $bootstrapPosition);
        $this->assertStringNotContainsString('@use', $entrypoint);
        $this->assertStringNotContainsString('.scss', $entrypoint);
    }

    public function test_master_keeps_only_the_styles_and_scripts_stacks(): void
    {
        $master = file_get_contents(resource_path('views/layouts/master.blade.php'));
        $home = file_get_contents(resource_path('views/frontend/home.blade.php'));

        $this->assertStringContainsString("@vite(['resources/css/frontend.css', 'resources/js/app.js'])", $master);
        $this->assertStringContainsString("@stack('styles')", $master);
        $this->assertStringContainsString("@stack('scripts')", $master);
        $this->assertStringNotContainsString("@yield('head')", $master);
        $this->assertStringNotContainsString("@stack('head')", $master);
        $this->assertStringContainsString("@push('styles')", $home);
        $this->assertStringContainsString("@vite('resources/css/pages/home.css')", $home);
        $this->assertStringNotContainsString("@section('main_id'", $home);
        $this->assertStringNotContainsString("@section('main_class'", $home);
    }

    public function test_homepage_signatures_use_dancing_script(): void
    {
        $fonts = file_get_contents(resource_path('css/fonts.css'));
        $theme = file_get_contents(resource_path('css/theme.css'));
        $homeStyles = file_get_contents(resource_path('css/pages/home.css'));

        $this->assertStringContainsString("@fontsource/dancing-script", $fonts);
        $this->assertStringContainsString("--site-font-script: 'Dancing Script'", $theme);
        $this->assertStringContainsString('font-family: var(--site-font-script);', $homeStyles);
    }

    public function test_homepage_does_not_override_bootstrap_container_width(): void
    {
        $homeStyles = file_get_contents(resource_path('css/pages/home.css'));
        $home = file_get_contents(resource_path('views/frontend/home.blade.php'));

        $this->assertStringNotContainsString(".container {\n        max-width: 1240px;", $homeStyles);
        $this->assertStringNotContainsString('max-width: 1240px;', $homeStyles);
        $this->assertStringNotContainsString('> .container', $homeStyles);
        $this->assertStringContainsString('<section class="hero" id="trang-chu"', $home);
        $this->assertStringContainsString('<div class="container hero-inner">', $home);
    }

    public function test_legacy_global_style_dumps_are_not_loaded(): void
    {
        $this->assertFileDoesNotExist(resource_path('css/frontend/site.css'));
        $this->assertFileDoesNotExist(resource_path('css/frontend/components.css'));
    }
}
