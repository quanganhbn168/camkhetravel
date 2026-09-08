<?php

namespace Tests\Feature;

use App\Filament\Resources\ServicePricings\Pages\EditServicePricing;
use App\Models\ServicePricing;
use App\Models\User;
use App\Support\Pricing\PricingCatalogPresenter;
use App\Support\Pricing\PricingJsonImporter;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExplicitServicePricingComparisonTest extends TestCase
{
    use DatabaseTransactions;

    private function sample(): ServicePricing
    {
        $pricing = ServicePricing::query()->firstOrFail();
        app(PricingJsonImporter::class)->import($pricing, file_get_contents(public_path('downloads/service-pricing-template.json')));

        return $pricing->fresh('packages.items');
    }

    public function test_template_imports_explicit_cells_independently_of_package_items(): void
    {
        $pricing = $this->sample();
        $result = app(PricingCatalogPresenter::class)->present($pricing);
        $this->assertCount(3, $result['comparison_rows']);
        $ids = $pricing->packages->pluck('id')->all();
        $flycam = $result['comparison_rows'][1]['cells'];
        $this->assertFalse($flycam[$ids[0]]['included']);
        $this->assertTrue($flycam[$ids[2]]['included']);
        $this->assertSame('2 buổi', $flycam[$ids[2]]['value']);
        $this->assertCount(1, $result['packages'][2]['items']);
    }

    public function test_comparison_only_import_keeps_prices_and_respects_false_on_highest_package(): void
    {
        $pricing = $this->sample();
        $before = $pricing->packages->map->getAttributes()->all();
        $ids = $pricing->packages->pluck('id')->all();
        app(PricingJsonImporter::class)->import($pricing, json_encode(['comparison' => [['name' => 'Tiêu chí riêng', 'cells' => [
            ['package' => $ids[0], 'included' => true, 'value' => 'Có ở gói cơ bản'],
            ['package' => $ids[2], 'included' => false],
        ]]]], JSON_THROW_ON_ERROR));
        $this->assertSame($before, $pricing->fresh('packages')->packages->map->getAttributes()->all());
        $pricing->packages()->whereKey($ids[2])->update(['sort_order' => 0]);
        $result = app(PricingCatalogPresenter::class)->present($pricing->fresh('packages.items'));
        $cells = $result['comparison_rows'][0]['cells'];
        $this->assertFalse($cells[$ids[2]]['included']);
        $this->assertTrue($cells[$ids[0]]['included']);
        $this->assertNull($cells[$ids[1]]['included']);
    }

    public function test_invalid_boolean_or_unknown_package_rolls_back_full_import(): void
    {
        $pricing = $this->sample();
        $ids = $pricing->packages->pluck('id')->all();
        foreach ([['package' => 'basic', 'included' => 'false'], ['package' => 'missing', 'included' => true]] as $cell) {
            try {
                app(PricingJsonImporter::class)->import($pricing, json_encode(['packages' => [['key' => 'basic', 'name' => 'New']], 'comparison' => [['name' => 'Test', 'cells' => [$cell]]]], JSON_THROW_ON_ERROR));
                $this->fail('Invalid comparison must be rejected.');
            } catch (\InvalidArgumentException) {
                $this->assertSame($ids, $pricing->packages()->pluck('id')->all());
            }
        }
    }

    public function test_admin_can_edit_true_false_cells_and_export_them(): void
    {
        $pricing = $this->sample();
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        $basicId = $pricing->packages->first()->id;
        $hydrated = Livewire::test(EditServicePricing::class, ['record' => $pricing->id]);
        $hydrated->call('save')->assertHasNoFormErrors();
        $saved = app(PricingCatalogPresenter::class)->present($pricing->fresh('packages.items'));
        $this->assertFalse($saved['comparison_rows'][1]['cells'][$basicId]['included']);
        $id = $pricing->packages->last()->id;
        Livewire::test(EditServicePricing::class, ['record' => $pricing->id])
            ->set('data.comparison_rows', [['name' => 'Flycam', 'cells' => [['package_id' => $id, 'included' => '0', 'value' => 'Không áp dụng']]]])
            ->call('save')->assertHasNoFormErrors();
        $result = app(PricingCatalogPresenter::class)->present($pricing->fresh('packages.items'));
        $this->assertFalse($result['comparison_rows'][0]['cells'][$id]['included']);
        Livewire::test(EditServicePricing::class, ['record' => $pricing->id])
            ->callAction('downloadComparison')->assertFileDownloaded('service-comparison-'.$pricing->id.'.json');
    }
}
