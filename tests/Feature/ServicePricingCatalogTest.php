<?php

namespace Tests\Feature;

use App\Models\PricingPackage;
use App\Models\Service;
use App\Models\ServicePricing;
use App\Support\Pricing\PricingJsonImporter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServicePricingCatalogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_service_has_one_pricing_catalog_with_packages_and_items(): void
    {
        $service = $this->service('san-xuat-phim-doanh-nghiep');
        $pricing = $service->pricingCatalog()->with('packages.items')->firstOrFail();

        $this->assertSame(3, $pricing->packages->count());
        $this->assertNotEmpty($pricing->packages->firstOrFail()->items);

        $this->get('/san-xuat-phim-doanh-nghiep')
            ->assertOk()
            ->assertSee('Bảng giá dịch vụ')
            ->assertSee('Chụp ảnh doanh nghiệp , TVC cơ bản')
            ->assertSee('Hình ảnh ,không gian công ty, doanh nghiệp')
            ->assertSee('13.000.000đ');
    }

    public function test_package_calculates_fixed_and_percentage_promotions(): void
    {
        $fixed = new PricingPackage([
            'list_price' => 10000000,
            'promotion_type' => 'fixed_price',
            'promotion_value' => 8500000,
        ]);
        $percent = new PricingPackage([
            'list_price' => 10000000,
            'promotion_type' => 'percent',
            'promotion_value' => 15,
        ]);

        $this->assertSame(8500000, $fixed->promotionPrice());
        $this->assertSame(8500000, $percent->promotionPrice());
        $this->assertTrue($fixed->hasPromotion());
        $this->assertTrue($percent->hasPromotion());
    }

    public function test_json_import_replaces_catalog_packages_and_items(): void
    {
        $pricing = ServicePricing::query()
            ->whereBelongsTo($this->service('san-xuat-phim-doanh-nghiep'))
            ->firstOrFail();

        $counts = app(PricingJsonImporter::class)->import($pricing, json_encode([
            'title' => 'Bảng giá TVC 2026',
            'description' => 'Bảng giá nhập nhanh từ JSON.',
            'packages' => [
                [
                    'name' => 'Gói tiêu chuẩn',
                    'list_price' => 10000000,
                    'promotion' => ['type' => 'percent', 'value' => 10],
                    'items' => [
                        ['name' => 'Kịch bản cơ bản'],
                        ['name' => 'Quay hình'],
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $pricing->refresh();
        $package = $pricing->packages()->with('items')->firstOrFail();

        $this->assertSame(['packages' => 1, 'items' => 2], $counts);
        $this->assertSame('Bảng giá TVC 2026', $pricing->title);
        $this->assertSame(9000000, $package->promotionPrice());
        $this->assertCount(2, $package->items);
        $this->assertNotEmpty($pricing->source_json);
    }

    public function test_super_admin_can_manage_service_pricing_catalogs(): void
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole(\Spatie\Permission\Models\Role::findOrCreate('super_admin'));

        $this->actingAs($user)
            ->get('/admin/service-pricings')
            ->assertOk()
            ->assertSee('Bảng giá dịch vụ');
    }

    private function service(string $slug): Service
    {
        return Service::query()
            ->whereHas('slugs', fn ($query) => $query->where('slug', $slug))
            ->firstOrFail();
    }
}
