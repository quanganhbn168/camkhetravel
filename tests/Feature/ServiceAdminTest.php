<?php

namespace Tests\Feature;

use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServiceAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Gate::before(fn () => true);
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
    }

    public function test_list_shows_sort_order_and_can_be_reordered(): void
    {
        $services = collect([3, 1, 2])->map(fn (int $order) => Service::create(['title' => 'Dịch vụ '.$order, 'status' => 'published', 'sort_order' => $order]));

        $list = Livewire::test(ListServices::class);
        $this->assertTrue($list->instance()->getTable()->isReorderable());
        $list->assertTableColumnExists('sort_order')
            ->assertCanSeeTableRecords($services->sortBy('sort_order'), inOrder: true);

        $this->get(ServiceResource::getUrl('index'))->assertOk()->assertSee('Thứ tự');
    }

    public function test_saving_an_edit_stays_on_the_edit_page(): void
    {
        $service = Service::create(['title' => 'Taxi Cẩm Khê', 'status' => 'published']);

        Livewire::test(EditService::class, ['record' => $service->id])
            ->set('data.hero_badge', 'Nhãn mới')
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNoRedirect();

        $this->assertSame('Nhãn mới', $service->fresh()->hero_badge);
    }

    public function test_creating_goes_to_the_edit_page_of_the_new_service(): void
    {
        $component = Livewire::test(CreateService::class)
            ->set('data.title', 'Taxi Yên Lập')
            ->set('data.status', 'draft')
            ->call('create')
            ->assertHasNoFormErrors();

        $service = Service::query()->where('title', 'Taxi Yên Lập')->firstOrFail();
        $component->assertRedirect(ServiceResource::getUrl('edit', ['record' => $service]));
    }
}
