<?php

namespace Tests\Feature;

use App\Filament\Resources\ServiceCategories\Pages\EditServiceCategory;
use App\Models\ServiceCategory;
use App\Models\User;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HomeFeaturedServicesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MenuSeeder::class);
    }

    public function test_homepage_only_receives_published_home_services(): void
    {
        \App\Models\Service::query()->update(['is_home' => false]);
        $category = ServiceCategory::create(['name' => 'Nhóm dịch vụ trang chủ QA', 'is_active' => true]);
        $shown = $category->services()->create(['title' => 'Dịch vụ được chọn QA', 'status' => 'published', 'is_home' => true, 'is_featured' => false]);
        $category->services()->create(['title' => 'Nổi bật nhưng không ở trang chủ QA', 'status' => 'published', 'is_home' => false, 'is_featured' => true]);
        $category->services()->create(['title' => 'Bản nháp trang chủ QA', 'status' => 'draft', 'is_home' => true]);
        $category->services()->create(['title' => 'Chưa đến ngày công bố QA', 'status' => 'published', 'published_at' => now()->addDay(), 'is_home' => true]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Dịch vụ của CamKheTravel')
            ->assertSee('Dịch vụ được chọn QA')
            ->assertDontSee('Bản nháp trang chủ QA')
            ->assertDontSee('Chưa đến ngày công bố QA')
            ->assertViewHas('services', fn ($services): bool => $services->modelKeys() === [$shown->id]);
    }

    public function test_homepage_category_flags_can_be_managed_independently_in_admin(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        $category = ServiceCategory::create(['name' => 'Nhóm quản trị trang chủ QA', 'is_active' => true, 'is_featured' => false, 'is_home' => false]);
        Livewire::test(EditServiceCategory::class, ['record' => $category->id])
            ->set('data.is_featured', true)->set('data.is_home', true)
            ->set('data.description', 'Mô tả ngắn riêng')
            ->set('data.body', '<h2>Nội dung danh mục kiểm thử</h2><p><strong>Nội dung có định dạng.</strong></p>')
            ->call('save')->assertHasNoFormErrors();
        $this->assertSame('Mô tả ngắn riêng', $category->fresh()->description);
        $this->assertStringContainsString('<strong>', $category->fresh()->body);
        $this->get(route('services.category', ['category' => $category->fresh()->slug]))
            ->assertOk()->assertSee('<h2>Nội dung danh mục kiểm thử</h2>', false);
        $this->assertTrue($category->fresh()->is_featured);
        $this->assertTrue($category->fresh()->is_home);
        Livewire::test(EditServiceCategory::class, ['record' => $category->id])
            ->set('data.is_home', false)->call('save');
        $this->assertTrue($category->fresh()->is_featured);
        $this->assertFalse($category->fresh()->is_home);
    }
}
