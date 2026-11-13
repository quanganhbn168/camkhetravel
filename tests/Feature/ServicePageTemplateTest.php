<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageServicePage;
use App\Models\Service;
use App\Models\User;
use App\Settings\ServicePageSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServicePageTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_sections_fall_back_to_the_shared_template(): void
    {
        $service = Service::create(['title' => 'Taxi Yên Lập', 'status' => 'published']);

        $this->get('/'.$service->slug)->assertOk()
            ->assertSee('Vì sao chọn Taxi Yên Lập?')
            ->assertSee('Đón khách tận nơi')
            ->assertSee('Quy trình đặt xe')
            ->assertSee('Liên hệ đặt xe')
            ->assertSee('Các dòng xe phục vụ')
            ->assertSee('Xe 7 chỗ');

        $this->assertNull($service->fresh()->benefit_items, 'Mẫu chỉ dùng để hiển thị, không ghi vào dịch vụ.');
    }

    public function test_a_service_keeps_its_own_content(): void
    {
        $service = Service::create([
            'title' => 'Taxi Cẩm Khê',
            'status' => 'published',
            'benefit_title' => 'Lý do riêng',
            'benefit_items' => [['title' => 'Tài xế người địa phương']],
            'vehicle_items' => [['title' => 'VinFast VF8']],
        ]);

        $this->get('/'.$service->slug)->assertOk()
            ->assertSee('Lý do riêng')
            ->assertSee('Tài xế người địa phương')
            ->assertDontSee('Đón khách tận nơi')
            ->assertSee('VinFast VF8')
            ->assertDontSee('Xe 7 chỗ')
            ->assertSee('Liên hệ đặt xe');
    }

    public function test_admin_can_edit_the_template(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);

        $this->get(ManageServicePage::getUrl())->assertOk()->assertSee('Mẫu trang dịch vụ');

        Livewire::test(ManageServicePage::class)
            ->set('data.process_title', 'Đặt {dich_vu} thế nào?')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('Đặt {dich_vu} thế nào?', app(ServicePageSettings::class)->process_title);

        $service = Service::create(['title' => 'Taxi Cẩm Khê', 'status' => 'published']);
        $this->get('/'.$service->slug)->assertOk()->assertSee('Đặt Taxi Cẩm Khê thế nào?');
    }
}
