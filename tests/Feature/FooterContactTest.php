<?php

namespace Tests\Feature;

use App\Settings\WebsiteSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FooterContactTest extends TestCase
{
    use DatabaseTransactions;

    public function test_footer_renders_one_phone_icon_and_a_pipe_between_phone_numbers(): void
    {
        $website = app(WebsiteSettings::class);
        $website->hotline = '0375 433 678';
        $website->contact_phone = '0973 494 999';
        $website->save();

        $html = $this->get(route('about'))
            ->assertOk()
            ->assertSee('0375 433 678')
            ->assertSee('0973 494 999')
            ->assertSee('<span class="text-slate-500" aria-hidden="true">|</span>', false)
            ->getContent();

        $this->assertSame(1, substr_count($html, 'data-footer-contact-icon="phone"'));
        $this->assertStringNotContainsString("\n+", $html);
    }
}
