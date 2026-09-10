<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Support\Landing\LandingPageBlocks;
use Awcodes\Curator\Models\Media;
use Database\Seeders\WebsiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WebsiteSeeder::class);
    }

    public function test_landing_page_renders_only_database_builder_content(): void
    {
        $media = $this->media();
        $landing = $this->landing($media);

        $this->get('/'.$landing->slug)
            ->assertOk()
            ->assertSee('Landing page kiểm thử')
            ->assertSee('data-landing-block="hero"', false)
            ->assertSee('data-landing-block="rich-text"', false)
            ->assertSee('data-landing-block="cta"', false)
            ->assertSee($media->url, false)
            ->assertDontSee('data-event-', false);
    }

    public function test_builder_sanitizes_external_links_and_ignores_unknown_blocks(): void
    {
        $landing = $this->landing(null, [
            ['type' => 'hero', 'data' => [
                'title' => 'Trang an toàn',
                'secondary_url' => 'javascript:alert(1)',
            ]],
            ['type' => 'not-a-real-block', 'data' => ['title' => 'Không hiển thị']],
        ]);

        $blocks = app(LandingPageBlocks::class)->prepare($landing->load('curatorMedia'));

        $this->assertCount(1, $blocks);
        $this->assertSame('#tu-van', $blocks[0]['data']['secondary_url']);
        $this->assertSame('hero', $blocks[0]['type']);
    }

    private function landing(?Media $media = null, ?array $sections = null): LandingPage
    {
        return LandingPage::query()->create([
            'title' => 'Landing page kiểm thử',
            'slug' => 'landing-page-kiem-thu-'.uniqid(),
            'excerpt' => 'Trang kiểm thử cho page builder.',
            'curator_media_id' => $media?->id,
            'status' => 'published',
            'published_at' => now()->subMinute(),
            'show_header' => false,
            'show_footer' => false,
            'sections' => $sections ?? [
                ['type' => 'hero', 'data' => [
                    'title' => 'Landing page kiểm thử',
                    'subtitle' => 'Nội dung lấy từ database.',
                    'media_id' => $media?->id,
                    'cta_url' => '#tu-van',
                ]],
                ['type' => 'rich_text', 'data' => [
                    'title' => 'Nội dung bổ sung',
                    'body' => '<p>Thông tin kiểm thử.</p>',
                ]],
                ['type' => 'cta', 'data' => [
                    'title' => 'Bắt đầu trao đổi',
                    'cta_label' => 'Liên hệ',
                    'cta_url' => '#tu-van',
                ]],
            ],
        ]);
    }

    private function media(): Media
    {
        return Media::query()->create([
            'disk' => 'public',
            'directory' => 'media/test',
            'name' => 'landing-test',
            'path' => 'media/test/landing-test.webp',
            'type' => 'image/webp',
            'ext' => 'webp',
            'width' => 1200,
            'height' => 630,
            'size' => 100,
        ]);
    }
}
