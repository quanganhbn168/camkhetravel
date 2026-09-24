<?php

namespace Tests\Feature;

use App\Settings\SystemPageSettings;
use App\Settings\WebsiteSettings;
use App\Support\Pages\SystemPageProfileResolver;
use Database\Seeders\HeroSlideSeeder;
use Database\Seeders\MediaSeeder;
use Database\Seeders\SystemPageSettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

class SystemPageSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            MediaSeeder::class,
            SystemPageSettingsSeeder::class,
        ]);
    }

    public function test_fixed_system_page_profiles_are_seeded_with_required_seo(): void
    {
        $settings = app(SystemPageSettings::class);

        foreach (['home', 'about', 'services', 'solutions', 'contact'] as $key) {
            $profile = $settings->{$key};

            $this->assertNotEmpty($profile['title'] ?? null, $key.' thiếu title.');
            $this->assertNotEmpty($profile['seo_title'] ?? null, $key.' thiếu seo_title.');
            $this->assertNotEmpty($profile['seo_description'] ?? null, $key.' thiếu seo_description.');
            $this->assertIsInt($profile['og_image_media_id'] ?? null, $key.' thiếu og_image_media_id.');
            $this->assertArrayHasKey('banner_media_id', $profile, $key.' thiếu banner_media_id.');
        }
    }

    public function test_solutions_route_uses_its_own_profile(): void
    {
        $settings = app(SystemPageSettings::class);
        $settings->solutions = [
            ...$settings->solutions,
            'title' => 'Giải pháp độc lập',
            'seo_title' => 'SEO giải pháp độc lập',
        ];
        $settings->save();

        $this->get(route('solutions.index'))
            ->assertOk()
            ->assertSee('data-system-page="solutions"', false)
            ->assertSee('<title>SEO giải pháp độc lập</title>', false)
            ->assertSee('Giải pháp độc lập')
            ->assertDontSee('Hệ sinh thái dịch vụ PCCC');
    }

    public function test_missing_required_system_page_seo_is_reported_instead_of_falling_back(): void
    {
        $settings = app(SystemPageSettings::class);
        $settings->contact = [
            ...$settings->contact,
            'seo_title' => '',
        ];
        $settings->save();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('system_pages.contact.seo_title');

        app(SystemPageProfileResolver::class)->require('contact');
    }

    public function test_missing_system_page_media_falls_back_without_crashing_the_public_page(): void
    {
        $settings = app(SystemPageSettings::class);
        $settings->contact = [
            ...$settings->contact,
            'og_image_media_id' => 999998,
            'banner_media_id' => 999999,
        ];
        $settings->save();

        $profile = app(SystemPageProfileResolver::class)->require('contact');

        $this->assertNull($profile['og_image_url']);
        $this->assertNull($profile['banner_url']);
        $this->get(route('contact'))->assertOk();
    }

    public function test_contact_page_uses_the_saved_map_embed_url_without_rewriting_it(): void
    {
        $website = app(WebsiteSettings::class);
        $website->google_maps_embed_url = 'https://maps.example.test/embed/raw?place=cam-khe&zoom=15';
        $website->save();

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('https://maps.example.test/embed/raw?place=cam-khe&zoom=15');
    }

    public function test_every_fixed_route_uses_its_matching_profile(): void
    {
        $settings = app(SystemPageSettings::class);
        $routes = [
            'home' => 'home',
            'about' => 'about',
            'services' => 'services.index',
            'solutions' => 'solutions.index',
            'contact' => 'contact',
        ];

        foreach ($routes as $key => $routeName) {
            $profile = $settings->{$key};
            $profile['title'] = 'Tiêu đề '.$key;
            $profile['seo_title'] = 'SEO '.$key;
            $settings->{$key} = $profile;
        }
        $settings->save();

        foreach ($routes as $key => $routeName) {
            $response = $this->get(route($routeName))
                ->assertOk()
                ->assertSee('data-system-page="'.$key.'"', false)
                ->assertSee('<title>SEO '.$key.'</title>', false);

            if ($key !== 'home') {
                $response->assertSee('Tiêu đề '.$key);
            }
        }
    }

    public function test_home_banner_and_hero_slides_are_independent(): void
    {
        $this->seed(HeroSlideSeeder::class);

        $bannerId = MediaSeeder::id('no-image');
        $settings = app(SystemPageSettings::class);
        $settings->home = [
            ...$settings->home,
            'banner_media_id' => $bannerId,
        ];
        $settings->save();

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('data-system-page="home"', false)
            ->assertSee('data-page-banner-image', false)
            ->assertSee('data-hero-section', false);
    }
}
