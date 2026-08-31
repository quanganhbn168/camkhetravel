<?php

namespace Tests\Feature;

use App\Models\ServicePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PricingPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_the_pricing_page_only_renders_packages_for_the_selected_service(): void
    {
        $catalogs = ServicePricing::query()
            ->whereHas('packages', fn ($query) => $query->where('is_active', true))
            ->with(['service.slugs', 'packages'])
            ->limit(2)
            ->get();

        $this->assertCount(2, $catalogs);
        $selectedCatalog = $catalogs->firstOrFail();
        $otherCatalog = $catalogs->last();
        $selectedPackage = $selectedCatalog->packages()->create([
            'name' => 'Gói riêng dịch vụ được chọn',
            'list_price' => 5000000,
            'price_unit' => '/ dự án',
            'is_active' => true,
            'sort_order' => 999,
        ]);
        $otherPackage = $otherCatalog->packages()->create([
            'name' => 'Gói của dịch vụ khác không được trộn vào',
            'list_price' => 7000000,
            'is_active' => true,
            'sort_order' => 999,
        ]);

        $this->get('/bang-gia?dich-vu='.$selectedCatalog->service->slug)
            ->assertOk()
            ->assertSee($selectedCatalog->service->title)
            ->assertSee($selectedPackage->name)
            ->assertDontSee($otherPackage->name)
            ->assertSee('Chỉ bảng giá của dịch vụ được chọn mới hiển thị bên dưới.')
            ->assertSee('OfferCatalog', false)
            ->assertSee('<link rel="canonical" href="'.rtrim(config('app.url'), '/').'/bang-gia">', false);

        $this->get('/en/bang-gia')->assertNotFound();
    }

    public function test_a_super_admin_can_manage_pricing_plans(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));

        $this->actingAs($user)
            ->get('/admin/pricing-plans')
            ->assertOk()
            ->assertSee('Bảng giá');
    }
}
