<?php

namespace Tests\Feature;

use Tests\TestCase;

class StaticFrontendDesignTest extends TestCase
{
    public function test_frontend_design_is_static_and_has_no_database_setting_layer(): void
    {
        $settingsConfig = file_get_contents(config_path('settings.php'));
        $layout = file_get_contents(resource_path('views/layouts/master.blade.php'));
        $styles = file_get_contents(resource_path('css/theme.css'));

        $this->assertStringNotContainsString('DesignSettings', $settingsConfig);
        $this->assertStringNotContainsString('<x-site-design-tokens', $layout);
        $this->assertStringContainsString('--site-primary: #07543f;', $styles);
        $this->assertFileDoesNotExist(app_path('Settings/DesignSettings.php'));
        $this->assertFileDoesNotExist(app_path('Support/Design/BrandPalette.php'));
        $this->assertFileDoesNotExist(app_path('View/Components/SiteDesignTokens.php'));
        $this->assertFileDoesNotExist(resource_path('views/components/site-design-tokens.blade.php'));
    }
}
