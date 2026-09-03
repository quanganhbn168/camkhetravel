<?php

namespace Tests\Feature;

use App\Settings\WebsiteSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FooterContactTest extends TestCase
{
    use DatabaseTransactions;

    public function test_footer_zalo_link_has_a_dedicated_contrast_safe_hover_style(): void
    {
        $website = app(WebsiteSettings::class);
        $website->zalo_url = 'https://zalo.me/0375433678';
        $website->save();

        $this->get(route('about'))
            ->assertOk()
            ->assertSee('footer-social footer-social--zalo', false);

        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertStringContainsString('.footer-social--zalo:hover img', $css);
        $this->assertStringContainsString('filter: brightness(0) saturate(100%);', $css);
    }

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
