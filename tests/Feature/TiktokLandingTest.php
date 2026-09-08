<?php

namespace Tests\Feature;

use App\Filament\Resources\LandingPages\Pages\EditLandingPage;
use App\Models\LandingPage;
use App\Models\User;
use App\Support\Landing\LandingRegistry;
use Awcodes\Curator\Models\Media;
use Database\Seeders\TiktokLandingSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TiktokLandingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_tiktok_uses_native_slug_managed_content_and_preserves_source_catalog(): void
    {
        $this->seed(TiktokLandingSeeder::class);
        $page = LandingPage::where('template_key', LandingRegistry::TIKTOK)->firstOrFail();
        $this->assertSame('xay-kenh-tiktok', $page->slug);
        $this->assertCount(7, $page->landing_content['projects']['items']);
        $this->assertCount(25, $page->landing_content['showreel']['videos']);
        $this->assertCount(9, $page->landing_content['samples']['categories']);
        $this->assertTrue($page->landing_content['promotion']['enabled']);
        $content = $page->landing_content;
        $content['hero']['title'] = 'TikTok nội dung từ CMS';
        $page->update(['landing_content' => $content]);
        $this->seed(TiktokLandingSeeder::class);
        $this->assertSame('TikTok nội dung từ CMS', $page->fresh()->landing_content['hero']['title']);
        $this->assertSame(1, LandingPage::where('template_key', LandingRegistry::TIKTOK)->count());
        $this->get('/xay-kenh-tiktok')->assertOk()->assertSeeText('TikTok nội dung từ CMS')
            ->assertSee('Tháng 10')->assertSee('8.000.000đ')->assertSee('10.000.000đ')
            ->assertSee('tht-landing-header', false)->assertSee('tht-landing-footer', false)
            ->assertSee('storage/media/landing/pages/tiktok/tht-media-team.webp', false)
            ->assertDontSee('wp-content', false)->assertDontSee('cdn.jsdelivr.net', false);
    }

    public function test_admin_can_edit_tiktok_without_losing_nested_videos_or_original_images(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        $page = LandingPage::where('template_key', LandingRegistry::TIKTOK)->firstOrFail();
        $before = $page->landing_content;
        $image = Media::query()->where('type', 'like', 'image/%')->firstOrFail();
        $editor = Livewire::test(EditLandingPage::class, ['record' => $page->id]);
        $rows = $editor->get('data.landing_content.pricing.rows');
        $rowKey = array_key_first($rows);
        $packageKey = array_key_first($rows[$rowKey]['packages']);
        $editor
            ->set('data.landing_content.hero.image_media_id', ['qa-image' => $image->toArray()])
            ->set('data.landing_content.hero.title', 'Tiêu đề chỉnh trong admin')
            ->set('data.landing_content.promotion.enabled', false)
            ->set("data.landing_content.pricing.rows.{$rowKey}.packages.{$packageKey}.phone_price", '8.500.000đ')
            ->call('save')->assertHasNoFormErrors();
        $after = $page->fresh()->landing_content;
        $this->assertSame($before['hero']['image'], $after['hero']['image']);
        $this->assertSame($image->id, (int) $after['hero']['image_media_id']);
        $this->assertCount(9, $after['samples']['categories']);
        $this->assertCount(25, $after['showreel']['videos']);
        $this->assertSame($before['samples']['categories'][0]['tiers'][0]['videos'][0]['url'], $after['samples']['categories'][0]['tiers'][0]['videos'][0]['url']);
        $this->assertSame('8.500.000đ', $after['pricing']['rows'][0]['packages'][0]['phone_price']);
        $this->get('/xay-kenh-tiktok')->assertOk()->assertSee('Tiêu đề chỉnh trong admin')->assertDontSee('id="uu-dai-tiktok"', false);
    }

    public function test_tiktok_form_saves_a_lead_and_returns_to_the_landing(): void
    {
        $page = LandingPage::where('template_key', LandingRegistry::TIKTOK)->firstOrFail();
        $this->post(route('contact.store'), [
            'from_landing_page' => '1', 'landing_page_id' => $page->id, 'landing_block_id' => 'tiktok-contact',
            'name' => 'TikTok QA', 'phone' => '0900000000', 'message' => 'Kiểm thử form trong transaction',
            'return_to' => '/xay-kenh-tiktok#lien-he',
        ])->assertRedirect('/xay-kenh-tiktok#lien-he')->assertSessionHas('success');
        $this->assertDatabaseHas('contact_requests', ['landing_page_id' => $page->id, 'landing_block_id' => 'tiktok-contact', 'name' => 'TikTok QA']);
    }
}
