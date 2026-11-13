<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Settings\WebsiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceLandingPageTest extends TestCase
{
    use RefreshDatabase;

    private function landing(): Service
    {
        return Service::create([
            'title' => 'Taxi Cẩm Khê',
            'status' => 'published',
            'excerpt' => 'Đặt xe đưa đón tại xã Cẩm Khê.',
            'hero_badge' => 'Dịch vụ vận chuyển tại Phú Thọ',
            'hero_subtitle' => 'Đặt xe nhanh – Đón tận nơi',
            'hero_points' => ['Đón tận nơi tại xã Cẩm Khê', 'Ghép xe hoặc bao xe riêng'],
            'package_items' => [
                ['title' => 'Ghép xe tiết kiệm', 'icon' => 'users', 'price' => 'Từ 150K', 'unit' => '/người/lượt'],
                ['title' => 'Bao xe riêng tư', 'price' => 'Từ 350K', 'unit' => '/chuyến'],
            ],
            'pricing_groups' => [[
                'destination' => 'Đi Việt Trì',
                'rows' => [
                    ['kind' => 'shared', 'label' => '1 khách', 'price' => '150.000đ – 200.000đ'],
                    ['kind' => 'private', 'label' => 'Xe 4 chỗ – 1 chiều', 'price' => '350.000đ'],
                    ['kind' => 'private', 'label' => 'Xe 4 chỗ – 2 chiều', 'price' => ''],
                ],
            ]],
            'usecase_items' => [['title' => 'Đi bệnh viện', 'icon' => 'hospital']],
            'area_title' => 'Khu vực nhận đón khách tại xã Cẩm Khê',
            'area_items' => [['title' => 'Đón tại chợ, trường học, cơ quan']],
        ]);
    }

    public function test_landing_sections_render_in_order(): void
    {
        $website = app(WebsiteSettings::class);
        $website->hotline = '0987 654 321';
        $website->zalo_url = 'https://zalo.me/0987654321';
        $website->save();

        $html = $this->get('/'.$this->landing()->slug)->assertOk()
            ->assertSee('Dịch vụ vận chuyển tại Phú Thọ')
            ->assertSee('Đặt xe nhanh – Đón tận nơi')
            ->assertSee('Đón tận nơi tại xã Cẩm Khê')
            ->assertSee('href="tel:0987654321"', false)
            ->assertSee('href="https://zalo.me/0987654321"', false)
            ->assertSee('Từ 150K')
            ->assertSee('/người/lượt')
            ->assertSee('fa-solid fa-users', false)
            ->getContent();

        $order = ['class="svc-hero"', 'id="goi-dich-vu"', 'id="bang-gia"', 'id="vi-sao-chon"', 'id="nhu-cau"', 'id="dong-xe"', 'id="khu-vuc"', 'id="quy-trinh"', 'id="gioi-thieu"', 'id="danh-gia"', 'id="dat-xe"'];
        $positions = array_map(fn (string $needle): int|false => strpos($html, $needle), $order);
        $this->assertNotContains(false, $positions, 'Thiếu section: '.json_encode(array_combine($order, $positions)));
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($sorted, $positions, 'Các section phải đúng thứ tự bố cục.');
    }

    public function test_pricing_groups_by_kind_and_shows_contact_for_missing_price(): void
    {
        $html = $this->get('/'.$this->landing()->slug)->assertOk()->getContent();
        $card = strstr(strstr($html, 'class="svc-price-card"'), '</article>', true);

        $this->assertStringContainsString('Đi Việt Trì', $card);
        $this->assertLessThan(strpos($card, 'Ghép khách'), strpos($card, 'Bao xe riêng'), 'Bao xe riêng đứng trước ghép khách.');
        $this->assertStringContainsString('<dt>Xe 4 chỗ – 2 chiều</dt><dd>Liên hệ</dd>', $card);
    }

    public function test_h1_pasted_in_content_is_demoted_so_pages_keep_one_h1(): void
    {
        $service = Service::create(['title' => 'Taxi Cẩm Khê', 'status' => 'published', 'body' => '<h1>Taxi Cẩm Khê uy tín</h1><p>a</p><h2>Giá</h2><p>b</p>']);
        $post = \App\Models\Post::create(['title' => 'Bài viết', 'status' => 'published', 'body' => '<h1>Tiêu đề trùng</h1><p>x</p>', 'published_at' => now()->subDay()]);
        $page = \App\Models\Page::create(['title' => 'Trang', 'slug' => 'trang-thu', 'status' => 'published', 'content' => '<h1>Trùng</h1><p>y</p>']);

        foreach (['/'.$service->slug, '/'.$post->slug, '/'.$page->slug] as $url) {
            $this->assertSame(1, preg_match_all('#<h1[\s>]#', $this->get($url)->assertOk()->getContent()), $url);
        }
        $this->get('/'.$service->slug)->assertSee('<h2 id="taxi-cam-khe-uy-tin">Taxi Cẩm Khê uy tín</h2>', false)
            ->assertSee('href="#taxi-cam-khe-uy-tin"', false);
    }

    public function test_excerpt_is_shown_once(): void
    {
        $service = Service::create(['title' => 'Taxi Cẩm Khê', 'status' => 'published', 'excerpt' => 'MO_TA_NGAN', 'body' => '<p>Nội dung</p>']);
        // Chỉ đếm phần hiển thị (<main>); thẻ meta/JSON-LD trong <head> dùng mô tả là đúng.
        $visible = fn (): string => strstr($this->get('/'.$service->slug)->getContent(), '<main');
        $this->assertSame(1, substr_count($visible(), 'MO_TA_NGAN'));

        $service->update(['hero_subtitle' => 'Câu phụ riêng']);
        $html = $visible();
        $this->assertSame(1, substr_count($html, 'MO_TA_NGAN'));
        $this->assertStringContainsString('Câu phụ riêng', $html);
    }

    public function test_hero_points_fall_back_to_why_choose_and_empty_sections_are_hidden(): void
    {
        $service = Service::create(['title' => 'Taxi Yên Lập', 'status' => 'published']);

        $html = $this->get('/'.$service->slug)->assertOk()
            ->assertSee('Đón khách tận nơi')
            ->getContent();
        $hero = strstr(strstr($html, 'class="svc-hero"'), '</section>', true);

        $this->assertStringContainsString('Đón khách tận nơi', $hero);
        foreach (['id="goi-dich-vu"', 'id="bang-gia"', 'id="nhu-cau"', 'id="khu-vuc"', 'id="gioi-thieu"'] as $hidden) {
            $this->assertStringNotContainsString($hidden, $html);
        }
        $this->assertStringContainsString('id="vi-sao-chon"', $html);
        $this->assertStringContainsString('id="dong-xe"', $html);
    }
}
