<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Settings\SystemPageSettings;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeHeroSlidePresentationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MenuSeeder::class);
    }

    public function test_homepage_displays_all_active_slides_in_admin_order(): void
    {
        HeroSlide::create(['title' => 'Slide đứng sau', 'sort_order' => 20, 'is_active' => true]);
        HeroSlide::create(['title' => 'Slide đứng trước', 'sort_order' => 10, 'is_active' => true]);
        HeroSlide::create(['title' => 'Slide đã tắt', 'sort_order' => 1, 'is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['Slide đứng trước', 'Slide đứng sau'])
            ->assertDontSee('Slide đã tắt');
    }

    public function test_active_slide_supplies_homepage_copy_and_secondary_link(): void
    {
        HeroSlide::create([
            'title' => 'Tiêu đề slide kiểm thử',
            'description' => 'Mô tả slide kiểm thử',
            'primary_label' => 'Liên hệ',
            'primary_url' => '/lien-he',
            'secondary_label' => 'Dịch vụ',
            'secondary_url' => '/dich-vu',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Tiêu đề slide kiểm thử')
            ->assertSee('Mô tả slide kiểm thử')
            ->assertSee('Liên hệ')
            ->assertSee('href="/lien-he"', false)
            ->assertSee('href="/dich-vu"', false)
            ->assertDontSee('hero__overlay', false);
    }

    public function test_each_slide_uses_its_own_image_and_configured_primary_destination(): void
    {
        $image = Media::create([
            'disk' => 'public', 'directory' => 'qa', 'name' => 'hero-managed',
            'path' => 'qa/hero-managed.webp', 'type' => 'image/webp', 'ext' => 'webp', 'size' => 100,
        ]);
        HeroSlide::create([
            'title' => 'Slide quản trị', 'curator_media_id' => $image->id,
            'primary_label' => 'Đến lịch trình riêng', 'primary_url' => '/lich-trinh-rieng', 'is_active' => true,
        ]);

        $response = $this->get('/')->assertOk();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $this->assertSame(MediaUrl::versioned($image), $xpath->query('//*[@id="trang-chu"]//img')->item(0)->getAttribute('src'));
        $this->assertSame(1, $xpath->query('//*[@id="trang-chu"]//a[@href="/lich-trinh-rieng" and not(@data-quote-type)]')->length);
    }

    public function test_image_only_slide_does_not_gain_fallback_copy_or_buttons(): void
    {
        HeroSlide::create(['is_active' => true]);
        $response = $this->get('/')->assertOk();
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $this->assertSame(0, $xpath->query('//*[@id="trang-chu"]//*[contains(concat(" ",normalize-space(@class)," ")," hero-copy ")]')->length);
    }

    public function test_empty_slider_uses_the_system_page_banner(): void
    {
        $image = Media::create([
            'disk' => 'public', 'directory' => 'qa', 'name' => 'page-banner',
            'path' => 'qa/page-banner.webp', 'type' => 'image/webp', 'ext' => 'webp', 'size' => 100,
        ]);
        $pages = app(SystemPageSettings::class);
        $pages->home = [...$pages->home, 'banner_media_id' => $image->id];
        $pages->save();

        $this->get('/')->assertOk()->assertSee('src="'.e(MediaUrl::versioned($image)).'"', false);
    }
}
