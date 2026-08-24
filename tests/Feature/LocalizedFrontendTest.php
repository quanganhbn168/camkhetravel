<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\Landing;
use Tests\TestCase;

class LocalizedFrontendTest extends TestCase
{
    public function test_prefixed_home_uses_the_requested_locale_and_canonical_url(): void
    {
        $this->get('/en')
            ->assertOk()
            ->assertSee('<html lang="en">', false)
            ->assertSee('<link rel="canonical" href="'.rtrim(config('app.url'), '/').'/en">', false)
            ->assertSee('/zh', false)
            ->assertSee('/ko', false);
    }

    public function test_localized_slug_falls_back_to_vietnamese_content_until_a_translation_exists(): void
    {
        $service = Landing::query()->published()->firstOrFail();

        $this->get('/en/'.$service->slug)
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.rtrim(config('app.url'), '/').'/en/'.$service->slug.'">', false);
    }

    public function test_hero_slides_have_seeded_multilingual_content(): void
    {
        $this->assertGreaterThanOrEqual(1, HeroSlide::query()->active()->count());
        $this->assertTrue(HeroSlide::query()->whereHas('translations', fn ($query) => $query->where('locale', 'en'))->exists());
    }
}
