<?php

namespace Tests\Feature;

use App\Settings\WebsiteSettings;
use Database\Seeders\WebsiteSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebManifestTest extends TestCase
{
    use RefreshDatabase;

    public function test_manifest_uses_the_current_cms_identity_and_global_primary_color(): void
    {
        $this->seed(WebsiteSettingsSeeder::class);
        $settings = app(WebsiteSettings::class);
        $settings->site_name = 'Website sau khi clone';
        $settings->save();

        preg_match('/--site-primary:\s*(#[a-f0-9]{6})/i', file_get_contents(resource_path('css/brand.css')), $color);

        $this->get('/site.webmanifest')->assertOk()
            ->assertHeader('Content-Type', 'application/manifest+json')
            ->assertJsonPath('name', 'Website sau khi clone')
            ->assertJsonPath('short_name', 'Website sau khi clone')
            ->assertJsonPath('theme_color', $color[1])
            ->assertJsonPath('start_url', '/');

        $this->assertFileDoesNotExist(public_path('site.webmanifest'));
    }
}
