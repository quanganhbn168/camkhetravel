<?php

namespace Tests\Feature;

use App\Settings\WebsiteSettings;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Database\Seeders\WebsiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class SharedBannerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteSeeder::class);
    }

    public function test_contact_prefers_its_own_banner_and_falls_back_when_cleared(): void
    {
        $website = app(WebsiteSettings::class);
        $image = Media::query()->create([
            'disk' => 'public', 'directory' => 'media/banner-test', 'name' => 'contact',
            'path' => 'media/banner-test/contact.webp', 'type' => 'image/webp', 'ext' => 'webp',
            'width' => 1600, 'height' => 400, 'size' => 100,
        ]);
        View::share('defaultBannerUrl', '/shared-banner-test.webp');
        $website->contact_image_media_id = $image->id;
        $this->get('/lien-he')->assertOk()
            ->assertSee('src="'.e(MediaUrl::versioned($image)).'"', false)
            ->assertDontSee('/shared-banner-test.webp');
        $website->contact_image_media_id = null;
        $this->get('/lien-he')->assertOk()->assertSee('src="/shared-banner-test.webp"', false);
    }
}
