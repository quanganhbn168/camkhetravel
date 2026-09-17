<?php

namespace Tests\Feature;

use App\Filament\Resources\ServiceCategories\Pages\EditServiceCategory;
use App\Models\ServiceCategory;
use App\Models\User;
use Database\Seeders\WebsiteSeeder;
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

        $this->seed(WebsiteSeeder::class);
    }

    public function test_featured_categories_require_both_flags_and_only_contain_published_home_services(): void
    {
        ServiceCategory::query()->update(['is_featured' => false]);
        $category = ServiceCategory::create(['name' => 'Nhóm media trang chủ QA', 'is_active' => true, 'is_featured' => true, 'is_home' => true]);
        $shown = $category->services()->create(['title' => 'Dịch vụ được chọn QA', 'status' => 'published', 'is_home' => true, 'is_featured' => false]);
        $category->services()->create(['title' => 'Nổi bật nhưng không ở trang chủ QA', 'status' => 'published', 'is_home' => false, 'is_featured' => true]);
        $category->services()->create(['title' => 'Bản nháp trang chủ QA', 'status' => 'draft', 'is_home' => true]);
        $category->services()->create(['title' => 'Chưa đến ngày công bố QA', 'status' => 'published', 'published_at' => now()->addDay(), 'is_home' => true]);

        foreach ([['is_featured' => false], ['is_home' => false], ['is_active' => false]] as $index => $flags) {
            $hidden = ServiceCategory::create([...['name' => 'Nhóm bị ẩn QA '.$index, 'is_active' => true, 'is_featured' => true, 'is_home' => true], ...$flags]);
            $hidden->services()->create(['title' => 'Dịch vụ thuộc nhóm ẩn QA '.$index, 'status' => 'published', 'is_home' => true]);
        }

        $this->get('/')->assertOk()->assertSee('Dịch vụ PCCC toàn diện')->assertDontSee('Các dịch vụ khác')
            ->assertViewHas('featuredServiceCategories', fn ($categories) => $categories->modelKeys() === [$category->id]
                && $categories->first()->services->modelKeys() === [$shown->id]);
    }

    public function test_homepage_category_flags_can_be_managed_independently_in_admin(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        $category = ServiceCategory::create(['name' => 'Nhóm quản trị trang chủ QA', 'is_active' => true, 'is_featured' => false, 'is_home' => false]);
        Livewire::test(EditServiceCategory::class, ['record' => $category->id])
            ->set('data.is_featured', true)->set('data.is_home', true)->call('save');
        $this->assertTrue($category->fresh()->is_featured);
        $this->assertTrue($category->fresh()->is_home);
        Livewire::test(EditServiceCategory::class, ['record' => $category->id])
            ->set('data.is_home', false)->call('save');
        $this->assertTrue($category->fresh()->is_featured);
        $this->assertFalse($category->fresh()->is_home);
    }
}
