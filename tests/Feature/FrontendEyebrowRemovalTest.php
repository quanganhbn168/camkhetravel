<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontendEyebrowRemovalTest extends TestCase
{
    public function test_public_frontend_has_no_eyebrow_labels_or_unused_homepage_eyebrow_setting(): void
    {
        $frontendViews = implode("\n", array_map(
            fn (string $path): string => file_get_contents($path),
            glob(resource_path('views/frontend/**/*.blade.php')),
        ));

        $this->assertStringNotContainsString('eyebrow', $frontendViews);
        $this->assertStringNotContainsString('section-kicker', $frontendViews);
        $this->assertStringNotContainsString('section-kicker', file_get_contents(resource_path('scss/pages/home.scss')));
        $this->assertStringNotContainsString('about_eyebrow', file_get_contents(app_path('Settings/HomepageSettings.php')));
        $this->assertStringNotContainsString('eyebrow', file_get_contents(app_path('Models/HeroSlide.php')));
        $this->assertStringNotContainsString('eyebrow', file_get_contents(app_path('Filament/Resources/HeroSlides/Schemas/HeroSlideForm.php')));
        $this->assertStringNotContainsString('eyebrow', file_get_contents(database_path('migrations/2026_09_11_004918_create_hero_slides_table.php')));
        $this->assertStringNotContainsString('eyebrow', file_get_contents(database_path('seeders/HeroSlideSeeder.php')));
    }
}
