<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Testimonial;
use App\Settings\HomepageSettings;
use App\Support\Media\MediaUrl;
use Awcodes\Curator\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_confirmed_testimonials_are_public(): void
    {
        Testimonial::create(['client_name' => 'Khách đã xác minh', 'quote' => 'Phản hồi thực tế QA', 'is_active' => true, 'is_illustrative' => false]);
        Testimonial::create(['client_name' => 'Khách minh họa', 'quote' => 'Không phải phản hồi thật QA', 'is_active' => true, 'is_illustrative' => true]);
        $this->get('/')->assertOk()->assertSee('Phản hồi thực tế QA')->assertDontSee('Không phải phản hồi thật QA');
    }

    public function test_audience_cards_use_managed_copy_media_and_only_published_home_service_links(): void
    {
        $category = ServiceCategory::create(['name' => 'Nhóm kiểm thử', 'is_active' => true]);
        $service = $category->services()->create(['title' => 'Dịch vụ tên không chứa từ khóa', 'status' => 'published', 'is_home' => true]);
        $draft = $category->services()->create(['title' => 'Dịch vụ bị ẩn QA', 'status' => 'draft', 'is_home' => true]);
        $image = $this->image('audience');
        $settings = app(HomepageSettings::class);
        $settings->audience_groups = [
            ['key' => 'trip', 'title' => 'Chuyến đi riêng QA', 'description' => 'Theo lịch của gia đình', 'media_id' => $image->id, 'service_ids' => [], 'cta_label' => 'Tư vấn chuyến đi'],
            ['key' => 'partner', 'title' => 'Đối tác QA', 'description' => 'Cùng tổ chức hành trình', 'media_id' => $image->id, 'service_ids' => [$service->id, $draft->id], 'cta_label' => 'Trao đổi hợp tác'],
            ['key' => 'wedding', 'title' => 'Ngày cưới QA', 'description' => 'Lịch trình ngày vui', 'service_ids' => [], 'cta_label' => 'Tư vấn xe cưới'],
        ];
        $settings->fleet_types = [['code' => 'qa', 'title' => 'Nhóm xe QA', 'features' => "Tiện ích QA", 'media_id' => $image->id, 'description' => 'Mô tả xe vừa lưu']];
        $settings->save();

        $response = $this->get('/')->assertOk()->assertSee('Chuyến đi riêng QA')->assertSee('Ngày cưới QA')
            ->assertSee('href="'.route('slug.show', ['slug' => $service->slug]).'"', false)->assertDontSee('Dịch vụ bị ẩn QA')
            ->assertSee('data-vehicle-image="'.e(MediaUrl::versioned($image)).'"', false)
            ->assertSee('Mô tả xe vừa lưu');
        $response->assertViewHas('audienceGroups', fn ($groups) => $groups->count() === 3 && $groups[1]['services']->first()->quote_type === 'partner');
        $this->assertStringNotContainsString('footer__cta"', $response->getContent());
        $this->get(route('contact'))->assertOk()->assertSee('footer__cta"', false);
    }

    public function test_installing_the_design_is_repeatable_and_preserves_cms_edits(): void
    {
        // Upgrade an existing database whose settings predate this release.
        \Illuminate\Support\Facades\DB::table('settings')->where('group', 'homepage')->whereIn('name', ['audience_groups', 'route_items', 'section_content', 'consultation_media_id', 'design_version'])->delete();
        app(HomepageSettings::class)->refresh();
        $this->artisan('homepage:install-design')->assertSuccessful();
        $settings = app(HomepageSettings::class);
        $this->assertCount(3, $settings->audience_groups);
        $this->assertNotNull($settings->audience_groups[0]['media_id']);
        $mediaCount = Media::count();
        $settings->audience_groups[0]['title'] = 'Tên anh đã chỉnh';
        $settings->audience_groups[0]['media_id'] = null;
        $settings->fleet_types[0]['media_id'] = null;
        $settings->save();
        HeroSlide::query()->first()->update(['title' => 'Hero đã chỉnh', 'curator_media_id' => null]);
        $this->artisan('homepage:install-design')->assertSuccessful();
        $settings->refresh();
        $this->assertSame('Tên anh đã chỉnh', $settings->audience_groups[0]['title']);
        $this->assertNull($settings->audience_groups[0]['media_id']);
        $this->assertNull($settings->fleet_types[0]['media_id']);
        $this->assertSame('Hero đã chỉnh', HeroSlide::first()->title);
        $this->assertNull(HeroSlide::first()->curator_media_id);
        $this->assertSame($mediaCount, Media::count());
        $this->seed(\Database\Seeders\HomepageSettingsSeeder::class);
        $this->seed(\Database\Seeders\HeroSlideSeeder::class);
        $this->assertSame('Tên anh đã chỉnh', $settings->refresh()->audience_groups[0]['title']);
        $this->assertSame('Hero đã chỉnh', HeroSlide::first()->title);
    }

    public function test_admin_saves_new_homepage_fields_without_losing_existing_content(): void
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole(\Spatie\Permission\Models\Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        $image = $this->image('admin');
        $original = app(HomepageSettings::class)->about_content;
        \Livewire\Livewire::test(\App\Filament\Pages\ManageSettings::class)
            ->set('data.site_name', 'Website kiểm thử')->set('data.company_name', 'Đơn vị kiểm thử')
            ->set('data.seo_title', 'Tiêu đề kiểm thử')->set('data.seo_description', 'Mô tả website kiểm thử')
            ->set('data.audience_groups', [['key' => 'partner', 'title' => 'Đối tác sửa từ admin', 'description' => 'Mô tả mới', 'media_id' => [$image->toArray()], 'service_ids' => [], 'cta_label' => 'Tư vấn']])
            ->set('data.consultation_title', 'Lời mời mới từ admin')
            ->set('data.consultation_media_id', [$image->toArray()])
            ->call('save')->assertHasNoFormErrors();
        $settings = app(HomepageSettings::class)->refresh();
        $this->assertSame($original, $settings->about_content);
        $this->get('/')->assertOk()->assertSee('Đối tác sửa từ admin')->assertSee('Lời mời mới từ admin');
    }

    public function test_mobile_hero_and_verified_statistics_follow_cms(): void
    {
        $image = $this->image('mobile');
        HeroSlide::create(['title' => 'Hero QA', 'mobile_media_id' => $image->id, 'is_active' => true]);
        $settings = app(HomepageSettings::class);
        $settings->stats = [['value' => '100', 'label' => 'Số chưa xác minh'], ['value' => '20', 'label' => 'Số đã xác minh', 'verified' => true]];
        $settings->save();
        $this->get('/')->assertOk()->assertSee('srcset="'.e(MediaUrl::versioned($image)).'"', false)
            ->assertSee('Số đã xác minh')->assertDontSee('Số chưa xác minh');
    }

    public function test_wedding_details_are_saved_in_existing_contact_request_flow(): void
    {
        $this->postJson(route('contact.store'), [
            'type' => 'wedding', 'name' => 'Khách kiểm thử', 'phone' => '0912345678',
            'pickup' => 'Nhà trai', 'destination' => 'Nhà gái', 'pickupTime' => '09:30',
            'weddingRole' => 'Đưa đón gia đình', 'consent' => true,
        ])->assertCreated()->assertJsonPath('success', true);
        $request = \App\Models\ContactRequest::latest('id')->first();
        $this->assertSame('09:30', $request->details['pickup_time'] ?? null);
        $this->assertSame('Đưa đón gia đình', $request->details['wedding_role'] ?? null);
        $this->assertSame('homepage_quote', $request->details['source']);
    }

    public function test_install_does_not_publish_faqs_when_admin_has_disabled_existing_questions(): void
    {
        \App\Models\Faq::create(['question' => 'Câu hỏi đã chủ động tắt', 'answer' => 'Nội dung đang rà soát', 'group' => 'homepage', 'is_active' => false]);
        $this->artisan('homepage:install-design')->assertSuccessful();
        $this->assertSame(0, \App\Models\Faq::homepage()->count());
        $this->assertSame(1, \App\Models\Faq::count());
    }

    private function image(string $name): Media
    {
        return Media::create(['disk' => 'public', 'directory' => 'qa', 'name' => $name, 'path' => "qa/{$name}.webp", 'type' => 'image/webp', 'ext' => 'webp', 'size' => 100, 'width' => 768, 'height' => 512])->fresh();
    }
}
