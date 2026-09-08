<?php

namespace Tests\Feature;

use App\Filament\Resources\ServicePricings\Pages\EditServicePricing;
use App\Models\ServicePricing;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServicePricingUxTest extends TestCase
{
    use DatabaseTransactions;

    public function test_reordering_packages_and_items_persists_when_saving(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        $pricing = ServicePricing::query()->has('packages', '>=', 2)->firstOrFail();
        $component = Livewire::test(EditServicePricing::class, ['record' => $pricing->id]);
        $packages = array_reverse($component->get('data.packages'), true);
        $firstKey = array_key_first($packages);
        $packages[$firstKey]['items'] = array_reverse($packages[$firstKey]['items'], true);
        $expectedPackages = array_column($packages, 'name');
        $expectedItems = array_column($packages[$firstKey]['items'], 'name');
        $component->set('data.packages', $packages)->call('save')->assertHasNoFormErrors();
        $saved = $pricing->packages()->with('items')->get();
        $this->assertSame($expectedPackages, $saved->pluck('name')->all());
        $this->assertSame($expectedItems, $saved->first()->items->pluck('name')->all());
    }

    public function test_selected_package_is_carried_into_contact_form_only_for_its_service(): void
    {
        $pricing = ServicePricing::query()->whereHas('packages', fn ($q) => $q->active())->whereHas('service', fn ($q) => $q->published())->firstOrFail();
        $package = $pricing->packages()->active()->firstOrFail();
        $this->get('/lien-he?service='.$pricing->service_id.'&plan='.$package->id)
            ->assertOk()->assertViewHas('pricingMessage', 'Tôi muốn được tư vấn gói '.$package->name.'.');
        $this->get('/lien-he?service=0&plan='.$package->id)
            ->assertOk()->assertViewHas('pricingMessage', '');
    }
}
