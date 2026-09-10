<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactRequests\Pages\ListContactRequests;
use App\Models\ContactRequest;
use App\Models\LandingPage;
use App\Models\Partner;
use App\Models\User;
use App\Support\Landing\LandingRegistry;
use Awcodes\Curator\Models\Media;
use Database\Seeders\BrandingLandingSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BrandingLandingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_partner_section_uses_active_logos_and_hides_when_none_are_available(): void
    {
        $this->seed(BrandingLandingSeeder::class);
        Partner::query()->update(['is_active' => false]);
        $this->get('/bo-nhan-dien-thuong-hieu')->assertOk()
            ->assertDontSee('id="branding-partners-title"', false);

        Partner::create(['name' => 'Partner without logo', 'is_active' => true, 'curator_media_id' => null]);
        $this->get('/bo-nhan-dien-thuong-hieu')->assertOk()
            ->assertDontSee('id="branding-partners-title"', false);

        $media = Media::query()->create([
            'disk' => 'public', 'directory' => 'qa', 'name' => 'partner-logo',
            'path' => 'qa/partner-logo.webp', 'type' => 'image/webp', 'ext' => 'webp',
            'width' => 176, 'height' => 80, 'size' => 100,
        ]);
        $partner = Partner::create(['name' => 'Managed branding partner', 'is_active' => true, 'curator_media_id' => $media->id]);
        Partner::create(['name' => 'Hidden branding partner', 'is_active' => false, 'curator_media_id' => $media->id]);
        $response = $this->get('/bo-nhan-dien-thuong-hieu')->assertOk()
            ->assertSee('id="branding-partners-title"', false)
            ->assertSee('Managed branding partner')
            ->assertDontSee('Hidden branding partner')
            ->assertDontSee('Partner without logo')
            ->assertDontSee('branding-reasons');
        $this->assertSame(2, substr_count($response->getContent(), 'class="branding-partner-row"'));

        $partner->update(['is_active' => false]);
        $this->get('/bo-nhan-dien-thuong-hieu')->assertOk()
            ->assertDontSee('id="branding-partners-title"', false);
    }

    public function test_branding_page_uses_managed_content_and_its_own_assets(): void
    {
        $this->seed(BrandingLandingSeeder::class);
        $page = LandingPage::query()->where('template_key', LandingRegistry::BRANDING)->firstOrFail();
        $content = $page->landing_content;
        $content['hero']['subtitle'] = 'Nội dung thương hiệu được quản lý';
        $page->update(['landing_content' => $content]);

        $this->get('/bo-nhan-dien-thuong-hieu')
            ->assertOk()
            ->assertSee('Nội dung thương hiệu được quản lý')
            ->assertSee('19.900.000 <small>VNĐ</small>', false)
            ->assertSee('ƯU ĐÃI GIẢM >50%')
            ->assertDontSee('ƯU ĐÃI CHỈ CÒN</', false)
            ->assertSee('40.000.000 VNĐ')
            ->assertSee('site-design-tokens', false)
            ->assertSee('hero-branding.webp', false)
            ->assertSee('branding-lead-form', false)
            ->assertSee('data-landing-success', false)
            ->assertSee('branding-form-success', false)
            ->assertSee('aria-label="Liên hệ nhanh"', false)
            ->assertDontSee('name="company"', false)
            ->assertDontSee('cdn.tailwindcss.com', false)
            ->assertDontSee('images.unsplash.com', false)
            ->assertDontSee('Form demo');

        $this->seed(BrandingLandingSeeder::class);
        $this->assertSame('Nội dung thương hiệu được quản lý', $page->fresh()->landing_content['hero']['subtitle']);
        $this->assertSame(1, LandingPage::query()->where('template_key', LandingRegistry::BRANDING)->count());
    }

    public function test_branding_form_persists_lead_and_returns_to_its_contact_section(): void
    {
        $this->seed(BrandingLandingSeeder::class);
        $page = LandingPage::query()->where('template_key', LandingRegistry::BRANDING)->firstOrFail();
        $this->post(route('contact.store'), [
            'from_landing_page' => '1',
            'landing_page_id' => $page->id,
            'landing_block_id' => 'branding-contact',
            'name' => 'Kiểm thử landing thương hiệu',
            'phone' => '0900000000',
            'message' => 'Tư vấn bộ nhận diện thương hiệu',
            'return_to' => '/bo-nhan-dien-thuong-hieu#lien-he',
        ])->assertRedirect('/bo-nhan-dien-thuong-hieu#lien-he')->assertSessionHas('success');

        $this->assertDatabaseHas('contact_requests', [
            'landing_page_id' => $page->id,
            'landing_block_id' => 'branding-contact',
            'name' => 'Kiểm thử landing thương hiệu',
        ]);

        $lead = ContactRequest::where('name', 'Kiểm thử landing thương hiệu')->latest('id')->firstOrFail();
        $this->assertNull($lead->company);
        $this->assertTrue($lead->landingPage->is($page));
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        Livewire::test(ListContactRequests::class)
            ->filterTable('landing_page_id', $page->id)
            ->assertCanSeeTableRecords([$lead])
            ->assertSee($page->title);

        $this->post(route('contact.store'), ['from_landing_page' => '1'])
            ->assertSessionHasErrors('phone');
    }

    public function test_branding_form_accepts_json_without_redirecting_the_landing_page(): void
    {
        $this->seed(BrandingLandingSeeder::class);
        $page = LandingPage::query()->where('template_key', LandingRegistry::BRANDING)->firstOrFail();

        $this->withHeaders([
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('contact.store'), [
            'from_landing_page' => '1',
            'landing_page_id' => $page->id,
            'landing_block_id' => 'branding-contact',
            'name' => 'Kiểm thử AJAX landing thương hiệu',
            'phone' => '0900000001',
            'message' => 'Tư vấn bộ nhận diện qua SweetAlert2',
            'return_to' => '/bo-nhan-dien-thuong-hieu#lien-he',
        ])->assertOk()
            ->assertJsonPath('message', __('site.contact_success'));

        $this->assertDatabaseHas('contact_requests', [
            'landing_page_id' => $page->id,
            'landing_block_id' => 'branding-contact',
            'name' => 'Kiểm thử AJAX landing thương hiệu',
        ]);
    }
}
