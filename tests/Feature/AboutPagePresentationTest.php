<?php

namespace Tests\Feature;

use App\Settings\AboutSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPagePresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_is_a_compact_managed_company_profile(): void
    {
        $settings = app(AboutSettings::class);
        $settings->story_title = 'Lĩnh vực hoạt động QA';
        $settings->story = '<p>Nội dung quản trị QA</p>';
        $settings->history = 'Lịch sử không đưa vào trang gọn QA';
        $settings->office_description = 'Văn phòng không đưa vào trang gọn QA';
        $settings->save();

        $this->get(route('about'))->assertOk()
            ->assertSee('Lĩnh vực hoạt động QA')
            ->assertSee('Nội dung quản trị QA')
            ->assertSee('about-summary', false)
            ->assertSee(route('contact'), false)
            ->assertDontSee('Lịch sử không đưa vào trang gọn QA')
            ->assertDontSee('Văn phòng không đưa vào trang gọn QA')
            ->assertDontSee('about-stats__grid', false)
            ->assertDontSee('image-placeholder', false);
    }
}
