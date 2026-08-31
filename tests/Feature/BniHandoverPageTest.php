<?php

namespace Tests\Feature;

use App\Models\BniChapter;
use App\Models\BniEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BniHandoverPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_bni_handover_page_uses_the_fixed_bni_identity(): void
    {
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
            ->assertSee('bni-handover-header-chapters')
            ->assertSee('bni-handover-page-nav')
            ->assertSee('id="video-su-kien"', false)
            ->assertSee('bni-overview__featured-media')
            ->assertSee('bni-chapter-video-list')
            ->assertSee('bni-chapter-widgets__grid')
            ->assertSee('bni-handover-overview-section')
            ->assertSee('bni-countdown__cta')
            ->assertSee('Đăng ký ngay')
            ->assertSee('Chưa gắn ảnh trong CMS BNI')
            ->assertSee('Chưa gắn video hoặc ảnh poster trong CMS BNI')
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

        $this->assertSame(4, substr_count($body, 'class="bni-chapter-video-item"'));
        $this->assertSame(0, substr_count($body, 'class="bni-chapter-video-item__link glightbox"'));
        $this->assertSame(0, substr_count($body, 'class="bni-chapter-video-item__overlay"'));
        $this->assertSame(4, substr_count($body, 'class="bni-chapter-widget"'));
        $this->assertSame(4, substr_count($body, '>Xem chi tiết <b'));
        $this->assertGreaterThan($overviewMediaStart, $chapterListStart);
        $this->assertLessThan($overviewMediaEnd, $chapterListStart);
        $this->assertStringNotContainsString('bni-chapter-showcases', $body);
        $this->assertStringNotContainsString('bni-chapter-video-item__label', $body);
        $this->assertFileExists(resource_path('images/bni/handover-network-wave.webp'));
        $this->assertFileExists(resource_path('images/bni/handover-city-network.webp'));
    }

    public function test_an_authenticated_user_does_not_see_a_logout_button_in_the_handover_content(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('bni.handover'))
            ->assertOk()
            ->assertDontSee('Đăng xuất');
    }
}
