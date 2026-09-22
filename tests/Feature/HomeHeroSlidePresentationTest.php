<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
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

    public function test_homepage_passes_hero_slide_models_to_the_view(): void
    {
        HeroSlide::create([
            'title' => 'Model slide kiểm thử',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertViewHas('heroSlides', fn ($slides): bool => $slides->first() instanceof HeroSlide);
    }

    public function test_content_slide_renders_table_content_overlay_and_ctas(): void
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
            ->assertSee('hero__overlay', false)
            ->assertSee('Tiêu đề slide kiểm thử')
            ->assertSee('Mô tả slide kiểm thử')
            ->assertSee('href="/lien-he"', false)
            ->assertSee('href="/dich-vu"', false);
    }

    public function test_image_only_slide_renders_the_image_without_text_or_overlay(): void
    {
        $media = Media::create([
            'disk' => 'public',
            'directory' => 'qa',
            'visibility' => 'public',
            'name' => 'hero-slide-image',
            'title' => 'Hero slide image',
            'path' => 'qa/hero-slide-image.jpg',
            'type' => 'image/jpeg',
            'ext' => 'jpg',
            'size' => 10,
        ]);

        HeroSlide::create([
            'curator_media_id' => $media->id,
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('hero__media', false)
            ->assertDontSee('hero__overlay', false)
            ->assertDontSee('hero__content', false);
    }
}
