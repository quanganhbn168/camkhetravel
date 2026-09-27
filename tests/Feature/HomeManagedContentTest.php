<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Settings\HomepageSettings;
use App\Settings\WebsiteSettings;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeManagedContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_reflects_saved_introduction_image_and_formatted_statistics(): void
    {
        $image = Media::create([
            'disk' => 'public', 'directory' => 'qa', 'name' => 'home-about',
            'path' => 'qa/home-about.webp', 'type' => 'image/webp', 'ext' => 'webp', 'size' => 100,
        ]);
        $website = app(WebsiteSettings::class);
        $website->about_image_media_id = $image->id;
        $website->save();
        $settings = app(HomepageSettings::class);
        $settings->about_title = 'Giới thiệu vừa lưu từ quản trị';
        $settings->about_content = "Nội dung dòng một\nNội dung dòng hai";
        $settings->stats = [
            ['verified' => true, 'value' => '24/7', 'label' => 'Hỗ trợ theo lịch'],
            ['verified' => true, 'prefix' => 'Hơn ', 'value' => '100', 'suffix' => '+', 'label' => 'Hành trình đã phục vụ'],
        ];
        $settings->save();

        $this->get('/')->assertOk()
            ->assertSee('Giới thiệu vừa lưu từ quản trị')->assertSee('Nội dung dòng hai')
            ->assertSee('src="'.e(MediaUrl::versioned($image)).'"', false)
            ->assertSee('24/7')->assertSee('Hơn 100+')->assertSee('Hành trình đã phục vụ');

        $settings->about_title = 'Giới thiệu đã cập nhật lần hai';
        $settings->save();
        $this->get('/')->assertOk()->assertSee('Giới thiệu đã cập nhật lần hai')->assertDontSee('Giới thiệu vừa lưu từ quản trị');
    }

    public function test_homepage_displays_both_managed_commitments_and_capabilities(): void
    {
        $settings = app(HomepageSettings::class);
        $settings->commitments = "Cam kết riêng thứ nhất\n\nCam kết riêng thứ hai";
        $settings->capabilities = "Năng lực riêng thứ nhất\nNăng lực riêng thứ hai";
        $settings->save();

        $this->get('/')->assertOk()->assertSeeInOrder(['Cam kết riêng thứ nhất', 'Cam kết riêng thứ hai'])
            ->assertSee('Năng lực riêng thứ nhất')->assertSee('Năng lực riêng thứ hai');
    }

    public function test_homepage_faq_uses_active_home_questions_in_admin_order_including_schema(): void
    {
        $settings = app(HomepageSettings::class);
        $settings->faq_title = 'Giải đáp về chuyến xe';
        $settings->faq_description = 'Mô tả FAQ được quản trị';
        $settings->save();
        Faq::create(['question' => 'Câu hỏi thứ hai?', 'answer' => 'Trả lời thứ hai.', 'group' => 'homepage', 'is_active' => true, 'sort_order' => 20]);
        Faq::create(['question' => 'Câu hỏi thứ nhất?', 'answer' => "Trả lời thứ nhất.\nDòng tiếp theo.", 'group' => 'homepage', 'is_active' => true, 'sort_order' => 10]);
        Faq::create(['question' => 'Câu hỏi đã ẩn?', 'answer' => 'Không hiển thị.', 'group' => 'homepage', 'is_active' => false]);
        Faq::create(['question' => 'Câu hỏi của dịch vụ?', 'answer' => 'Không thuộc trang chủ.', 'group' => 'detail', 'faqable_type' => 'service', 'faqable_id' => 999, 'is_active' => true]);

        $response = $this->get('/')->assertOk()->assertSee('Giải đáp về chuyến xe')->assertSee('Mô tả FAQ được quản trị')
            ->assertDontSee('Câu hỏi đã ẩn?')->assertDontSee('Câu hỏi của dịch vụ?');
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath = new \DOMXPath($dom);
        $summaries = $xpath->query('//*[@id="cau-hoi-thuong-gap"]//summary');
        $this->assertSame(2, $summaries->length);
        $this->assertSame('Câu hỏi thứ nhất?', trim($summaries->item(0)->textContent));
        $this->assertSame('Câu hỏi thứ hai?', trim($summaries->item(1)->textContent));
        $response->assertSee('FAQPage');
    }

    public function test_empty_optional_content_does_not_leave_empty_home_sections(): void
    {
        $settings = app(HomepageSettings::class);
        $settings->about_title = '';
        $settings->about_content = '';
        $settings->stats = [];
        $settings->commitments = '';
        $settings->commitment_items = [];
        $settings->capabilities = '';
        $settings->save();
        $website = app(WebsiteSettings::class);
        $website->about_image_media_id = null;
        $website->save();

        $this->get('/')->assertOk()->assertDontSee('id="gioi-thieu"', false)
            ->assertDontSee('data-home-stats', false)->assertDontSee('id="cau-hoi-thuong-gap"', false)
            ->assertDontSee('FAQPage');
    }
}
