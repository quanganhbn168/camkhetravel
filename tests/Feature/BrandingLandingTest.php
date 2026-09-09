<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactRequests\Pages\ListContactRequests;
use App\Models\ContactRequest;
use App\Models\LandingPage;
use App\Models\User;
use App\Support\Landing\LandingRegistry;
use Database\Seeders\BrandingLandingSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BrandingLandingTest extends TestCase
{
    use DatabaseTransactions;

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
            ->assertSee('GIẢM >50%')
            ->assertSee('40.000.000 VNĐ')
            ->assertSee('site-design-tokens', false)
            ->assertSee('hero-branding.webp', false)
            ->assertSee('branding-lead-form', false)
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
}
