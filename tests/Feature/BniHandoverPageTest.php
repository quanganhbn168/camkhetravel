<?php

namespace Tests\Feature;

use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BniHandoverPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_bni_handover_page_uses_the_fixed_bni_identity(): void
    {
        $eventIds = BniEvent::query()->where('type', 'handover')->pluck('id');
        $videoIds = DB::table('bni_event_videos')->whereIn('bni_event_id', $eventIds)->pluck('id');
        $chapterIds = BniChapter::query()->pluck('id');

        DB::table('media')->whereIn('model_type', ['bni-event', BniEvent::class])->whereIn('model_id', $eventIds)->delete();
        DB::table('media')->whereIn('model_type', ['bni-event-video', 'App\\Models\\BniEventVideo'])->whereIn('model_id', $videoIds)->delete();
        DB::table('media')->whereIn('model_type', ['bni-chapter', BniChapter::class])->whereIn('model_id', $chapterIds)->delete();
        DB::table('bni_event_videos')->whereIn('id', $videoIds)->update(['external_url' => null]);

        BniEvent::query()->where('type', 'handover')->update([
            'hero_media_id' => null,
            'video_media_id' => null,
            'video_poster_media_id' => null,
            'video_url' => null,
        ]);
        BniChapter::query()->update([
            'logo_media_id' => null,
            'cover_media_id' => null,
            'video_media_id' => null,
            'video_url' => null,
        ]);

        $response = $this->get(route('bni.handover'))
            ->assertOk()
            ->assertSee('bni-handover-menu-link')
            ->assertSee('bni-logo-red.svg')
            ->assertSee('LỄ CHUYỂN GIAO')
            ->assertSee('bni-handover-header-brand')
            ->assertDontSee('bni-handover-header-chapters')
            ->assertSee('bni-handover-page-nav')
            ->assertSee('id="video-gioi-thieu"', false)
            ->assertSee('bni-overview__featured-media')
            ->assertSee('bni-chapter-video-list')
            ->assertSee('bni-chapter-widgets__grid')
            ->assertSee('bni-handover-overview-section')
            ->assertSee('bni-countdown__cta')
            ->assertSee('Đăng ký ngay')
            ->assertSee('Chưa gắn ảnh trong CMS BNI')
            ->assertSee('Chưa gắn video hoặc ảnh cover trong mục Video giới thiệu')
            ->assertSee('border-t border-white/15 bg-midnight')
            ->assertDontSee('bni-handover-chapter-nav')
            ->assertSee('KINHBAC')
            ->assertSee('KBG')
            ->assertSee('IMPACT')
            ->assertSee('FAMOUS');

        $body = $response->getContent();
        $overviewMediaStart = strpos($body, '<aside class="bni-overview__video"');
        $overviewMediaEnd = strpos($body, '</aside>', $overviewMediaStart);
        $chapterListStart = strpos($body, 'class="bni-chapter-video-list"');
        $introVideoStart = strpos($body, 'id="video-gioi-thieu"');

        $this->assertSame(4, substr_count($body, 'class="bni-chapter-video-item"'));
        $this->assertSame(0, substr_count($body, 'class="bni-chapter-video-item__link glightbox"'));
        $this->assertSame(0, substr_count($body, 'class="bni-chapter-video-item__overlay"'));
        $this->assertSame(4, substr_count($body, 'class="bni-chapter-widget"'));
        $this->assertSame(4, substr_count($body, '>Xem chi tiết <b'));
        $this->assertGreaterThan($overviewMediaStart, $chapterListStart);
        $this->assertLessThan($overviewMediaEnd, $chapterListStart);
        $this->assertGreaterThan($overviewMediaEnd, $introVideoStart);
        $this->assertStringNotContainsString('bni-chapter-showcases', $body);
        $this->assertStringNotContainsString('bni-chapter-video-item__label', $body);
        $this->assertFileExists(resource_path('images/bni/bni-kv-milk-red.webp'));
    }

    public function test_the_bni_frontend_palette_is_limited_to_milk_white_red_and_black_text(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertIsString($css);
        $this->assertStringContainsString('--site-color-bni-white: #fff8f1;', $css);
        $this->assertStringContainsString("url('../images/bni/bni-kv-milk-red.webp')", $css);
        $this->assertStringNotContainsString('body.bni-app-shell > .relative.z-40', $css);
        $this->assertStringContainsString("url('../images/bni/handover-city-network.webp')", $css);
        $this->assertStringContainsString("url('../images/bni/handover-network-wave.webp')", $css);
        $this->assertStringNotContainsString('background: var(--bni-black)', $css);
        $this->assertStringNotContainsString('color-mix(in srgb, var(--bni-black)', $css);
        $this->assertStringNotContainsString('body.bni-app-shell :is(.floating-action--zalo, .footer-social--zalo) img { filter:', $css);
    }

    public function test_an_authenticated_user_does_not_see_a_logout_button_in_the_handover_content(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('bni.handover'))
            ->assertOk()
            ->assertDontSee('Đăng xuất');
    }
}
