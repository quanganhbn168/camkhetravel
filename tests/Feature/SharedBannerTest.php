<?php

namespace Tests\Feature;

use App\Settings\SystemPageSettings;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class SharedBannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MenuSeeder::class);
    }

    public function test_contact_uses_only_its_optional_page_banner(): void
    {
        $settings = app(SystemPageSettings::class);
        $image = Media::query()->create([
            'disk' => 'public', 'directory' => 'media/banner-test', 'name' => 'contact',
            'path' => 'media/banner-test/contact.webp', 'type' => 'image/webp', 'ext' => 'webp',
            'width' => 1600, 'height' => 400, 'size' => 100,
        ]);
        View::share('defaultBannerUrl', '/shared-banner-test.webp');
        $settings->contact = [...$settings->contact, 'banner_media_id' => $image->id];
        $settings->save();
        $this->get('/lien-he')->assertOk()
            ->assertSee('src="'.e(MediaUrl::versioned($image)).'"', false)
            ->assertDontSee('/shared-banner-test.webp');
        $settings->contact = [...$settings->contact, 'banner_media_id' => null];
        $settings->save();
        $this->get('/lien-he')->assertOk()
            ->assertDontSee('data-page-banner-image', false)
            ->assertDontSee('/shared-banner-test.webp');
    }
}
