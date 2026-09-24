<?php

namespace Tests\Feature;

use Tests\TestCase;

class FrontendEyebrowRemovalTest extends TestCase
{
    public function test_homepage_has_no_legacy_eyebrow_copy_or_settings(): void
    {
        $homeView = file_get_contents(resource_path('views/frontend/home.blade.php'));

        $this->assertStringNotContainsString('eyebrow', $homeView);
        $this->assertStringNotContainsString('section-kicker', $homeView);
        $this->assertStringNotContainsString('section-kicker', file_get_contents(resource_path('css/pages/home.css')));
        $this->assertStringNotContainsString('about_eyebrow', file_get_contents(app_path('Settings/HomepageSettings.php')));
        $this->assertStringNotContainsString('eyebrow', file_get_contents(app_path('Models/HeroSlide.php')));
        $this->assertStringNotContainsString('eyebrow', file_get_contents(app_path('Filament/Resources/HeroSlides/Schemas/HeroSlideForm.php')));
        $this->assertStringNotContainsString('eyebrow', file_get_contents(database_path('migrations/2026_09_11_004918_create_hero_slides_table.php')));
        $this->assertStringNotContainsString('eyebrow', file_get_contents(database_path('seeders/HeroSlideSeeder.php')));
    }
}
