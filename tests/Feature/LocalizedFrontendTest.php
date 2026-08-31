<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Support\Localization\LanguageCatalog;
use Tests\TestCase;

class LocalizedFrontendTest extends TestCase
{
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
        $service = Service::query()->published()->firstOrFail();

        $this->get('/en')->assertNotFound();
        $this->get('/en/'.$service->slug)->assertNotFound();
        $this->get('/zh/bang-gia')->assertNotFound();
        $this->get('/ko/le-chuyen-giao')->assertNotFound();
    }
}
