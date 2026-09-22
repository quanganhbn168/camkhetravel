<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontendShellStandardTest extends TestCase
{
    public function test_frontend_uses_a_single_button_scale_and_a_non_sticky_topbar(): void
    {
        $foundation = file_get_contents(resource_path('scss/frontend.scss'));
        $brand = file_get_contents(resource_path('scss/frontend.scss'));
        $runtime = file_get_contents(resource_path('js/app.js'));
        $header = file_get_contents(resource_path('views/partials/header.blade.php'));
        $actions = file_get_contents(resource_path('views/partials/header/actions.blade.php'));
        $headerStyles = file_get_contents(resource_path('scss/components/_header.scss'));
        $entrypoint = file_get_contents(resource_path('scss/frontend.scss'));
        $homeStyles = file_get_contents(resource_path('scss/pages/home.scss'));
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
        $this->assertStringNotContainsString("@use '../css/frontend/site.css';", $entrypoint);
        $this->assertStringContainsString('.header-sticky.is-scroll-hidden:not(:focus-within)', $headerStyles);
        $this->assertStringContainsString("header.classList.toggle('is-scroll-hidden', currentScroll > previousScroll)", $runtime);
        $this->assertStringNotContainsString('final-cta', $home);
        $this->assertStringNotContainsString('final-cta', $homeStyles);
        $this->assertStringContainsString('Liên hệ tư vấn miễn phí', $home);
        $this->assertStringContainsString('Gửi thông tin công trình để đội ngũ kỹ thuật tư vấn giải pháp tối ưu.', $home);
    }

    public function test_shared_header_and_home_hero_each_have_one_style_owner(): void
    {
        $headerStyles = file_get_contents(resource_path('scss/components/_header.scss'));
        $homeStyles = file_get_contents(resource_path('scss/pages/home.scss'));
        $entrypoint = file_get_contents(resource_path('scss/frontend.scss'));
        $views = implode("\n", array_map(
            static fn (\SplFileInfo $file): string => file_get_contents($file->getPathname()),
            iterator_to_array(new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS)
            ))
        ));

        $this->assertStringContainsString('.header-sticky', $headerStyles);
        $this->assertStringContainsString('.topbar', $headerStyles);
        $this->assertStringContainsString('.hero', $homeStyles);
        $this->assertStringNotContainsString("@use '../css/frontend/site.css';", $entrypoint);
        $this->assertStringNotContainsString('button-dark', $views);
    }

    public function test_bootstrap_is_compiled_before_website_layers(): void
    {
        $entrypoint = file_get_contents(resource_path('scss/frontend.scss'));

        $bootstrapPosition = strpos($entrypoint, "@import 'bootstrap/scss/bootstrap';");
        $headerPosition = strpos($entrypoint, "@include meta.load-css('components/header');");

        $this->assertNotFalse($bootstrapPosition);
        $this->assertNotFalse($headerPosition);
        $this->assertLessThan($headerPosition, $bootstrapPosition);
        $this->assertStringNotContainsString("@use '../css/frontend/components.css';", $entrypoint);
        $this->assertStringNotContainsString("bootstrap/dist/css/bootstrap.css", $entrypoint);
    }

    public function test_homepage_has_its_own_vite_style_entry(): void
    {
        $entrypoint = file_get_contents(resource_path('scss/frontend.scss'));
        $home = file_get_contents(resource_path('views/frontend/home.blade.php'));

        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
        $this->assertArrayHasKey('resources/scss/pages/home.scss', $manifest);
        $this->assertStringContainsString("@vite('resources/scss/pages/home.scss')", $home);
        $this->assertStringNotContainsString("@use 'pages/home';", $entrypoint);
    }

    public function test_homepage_does_not_override_bootstrap_container_width(): void
    {
        $homeStyles = file_get_contents(resource_path('scss/pages/home.scss'));
        $home = file_get_contents(resource_path('views/frontend/home.blade.php'));

        $this->assertStringNotContainsString(".container {\n        max-width: 1240px;", $homeStyles);
        $this->assertStringNotContainsString('max-width: 1240px;', $homeStyles);
        $this->assertStringNotContainsString('> .container', $homeStyles);
        $this->assertStringContainsString(
            '<section class="section-space consultation" id="tu-van">' . "\n" . '        <div class="container">',
            $home
        );
    }

    public function test_legacy_global_style_dumps_are_not_loaded(): void
    {
        $this->assertFileDoesNotExist(resource_path('css/frontend/site.css'));
        $this->assertFileDoesNotExist(resource_path('css/frontend/components.css'));
    }
}
