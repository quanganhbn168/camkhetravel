<?php

namespace Tests\Feature;

use App\Models\Intro;
use App\Support\Localization\LanguageCatalog;
use Awcodes\Curator\Models\Media;
use Database\Seeders\WebsiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizedFrontendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteSeeder::class);
    }

    public function test_the_public_website_is_vietnamese_only(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="vi">', false)
            ->assertDontSee('hreflang=', false)
            ->assertDontSee('Chọn ngôn ngữ');

        $this->assertSame(['vi'], app(LanguageCatalog::class)->active()->keys()->values()->all());
    }

    public function test_old_locale_prefixed_urls_are_not_public_routes(): void
    {
        $this->get('/en')->assertNotFound();
        $this->get('/en/example')->assertNotFound();
        $this->get('/zh/bang-gia')->assertNotFound();
        $this->get('/ko/le-chuyen-giao')->assertNotFound();
    }

    public function test_intro_pages_use_the_global_slug_table_and_curator_media(): void
    {
        $media = Media::query()->create([
            'disk' => 'public',
            'directory' => 'media/test',
            'name' => 'intro-test',
            'path' => 'media/test/intro-test.webp',
            'type' => 'image/webp',
            'ext' => 'webp',
            'width' => 1200,
            'height' => 630,
            'size' => 100,
        ]);
        $intro = Intro::query()->create([
            'title' => 'Bài giới thiệu kiểm thử',
            'slug' => 'bai-gioi-thieu-kiem-thu',
            'summary' => 'Nội dung giới thiệu.',
            'content' => '<p>Nội dung kiểm thử.</p>',
            'curator_media_id' => $media->id,
            'kind' => 'article',
            'is_active' => true,
            'published_at' => now()->subMinute(),
        ]);

        $this->assertDatabaseHas('slugs', [
            'sluggable_type' => 'intro',
            'sluggable_id' => $intro->id,
            'slug' => 'bai-gioi-thieu-kiem-thu',
            'locale' => 'vi',
        ]);
        $this->get('/bai-gioi-thieu/bai-gioi-thieu-kiem-thu')
            ->assertOk()
            ->assertSee($intro->title)
            ->assertSee($media->url, false);
    }
}
