<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Support\Media\MediaUrl;
use App\Support\Seo\FrontendSeoBuilder;
use Awcodes\Curator\Models\Media;
use Database\Seeders\WebsiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShareImageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteSeeder::class);
    }

    public function test_landing_share_image_is_read_from_curator_without_replacing_the_hero_media(): void
    {
        $hero = $this->media('hero');
        $share = $this->media('share');
        $landing = LandingPage::query()->create([
            'title' => 'Landing SEO kiểm thử',
            'slug' => 'landing-seo-kiem-thu',
            'curator_media_id' => $hero->id,
            'seo_image_media_id' => $share->id,
            'status' => 'published',
            'published_at' => now()->subMinute(),
            'show_header' => false,
            'show_footer' => false,
        ]);

        $seo = app(FrontendSeoBuilder::class)->landingPage($landing->load('seoImageMedia'));

        $this->assertSame(MediaUrl::versioned($share), $seo['image']);
        $this->assertSame($hero->id, $landing->curator_media_id);
        $this->assertNotSame($hero->id, $landing->seo_image_media_id);
    }

    private function media(string $name): Media
    {
        return Media::query()->create([
            'disk' => 'public',
            'directory' => 'media/seo-test',
            'name' => $name,
            'path' => 'media/seo-test/'.$name.'.webp',
            'type' => 'image/webp',
            'ext' => 'webp',
            'width' => 1200,
            'height' => 630,
            'size' => 100,
        ]);
    }
}
