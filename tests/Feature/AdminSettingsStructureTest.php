<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminSettingsStructureTest extends TestCase
{
    public function test_settings_keeps_home_and_about_tabs_without_the_design_tab(): void
    {
        $settingsPage = file_get_contents(app_path('Filament/Pages/ManageSettings.php'));

        $this->assertStringContainsString("Tab::make('Trang chủ')", $settingsPage);
        $this->assertStringContainsString("Tab::make('Trang Giới thiệu')", $settingsPage);
        $this->assertStringNotContainsString("Tab::make('Giao diện')", $settingsPage);
        $this->assertStringNotContainsString('->schema($this->designSchema())', $settingsPage);
    }

    public function test_settings_manages_all_six_fixed_page_profiles_without_locale_fields(): void
    {
        $settingsPage = file_get_contents(app_path('Filament/Pages/ManageSettings.php'));

        $this->assertStringContainsString("Tab::make('Trang hệ thống')", $settingsPage);

        foreach (['home', 'about', 'services', 'solutions', 'contact', 'projects'] as $page) {
            $this->assertStringContainsString("'{$page}' =>", $settingsPage);
        }

        foreach (['title', 'seo_title', 'seo_description', 'og_image_media_id', 'banner_media_id'] as $field) {
            $this->assertStringContainsString("\$prefix.'.{$field}'", $settingsPage);
        }

        $this->assertStringNotContainsString('.vi', $settingsPage);
    }
}
