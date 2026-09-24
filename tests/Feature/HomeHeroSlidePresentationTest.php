<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
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

    public function test_homepage_passes_the_first_active_hero_slide_to_the_view(): void
    {
        $slide = HeroSlide::create([
            'title' => 'Model slide kiểm thử',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertViewHas('heroSlide', fn (?HeroSlide $heroSlide): bool => $heroSlide?->is($slide));
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
            ->assertSee('href="/dich-vu"', false)
            ->assertDontSee('hero__overlay', false);
    }
}
