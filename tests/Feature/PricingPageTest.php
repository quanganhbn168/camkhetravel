<?php

namespace Tests\Feature;

use App\Models\PricingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PricingPageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_active_pricing_plans_are_rendered_with_offer_catalog_schema(): void
    {
        $visiblePlan = PricingPlan::query()->create([
            'name' => 'Gói kiểm thử hiển thị',
            'description' => 'Phạm vi dành cho kiểm thử trang bảng giá.',
            'price' => 5000000,
            'price_unit' => '/ dự án',
            'features' => ['Hạng mục kiểm thử'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        PricingPlan::query()->create([
            'name' => 'Gói không hiển thị',
            'is_active' => false,
        ]);

        $this->get('/bang-gia')
            ->assertOk()
            ->assertSee($visiblePlan->name)
            ->assertSee('Hạng mục kiểm thử')
            ->assertSee('OfferCatalog', false)
            ->assertDontSee('Gói không hiển thị');

        $this->get('/en/bang-gia')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.rtrim(config('app.url'), '/').'/en/bang-gia">', false);
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
