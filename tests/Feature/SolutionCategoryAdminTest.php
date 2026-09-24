<?php

namespace Tests\Feature;

use App\Filament\Resources\SolutionCategories\Pages\CreateSolutionCategory;
use App\Filament\Resources\SolutionCategories\Pages\EditSolutionCategory;
use App\Filament\Resources\SolutionCategories\Pages\ListSolutionCategories;
use App\Models\Solution;
use App\Models\SolutionCategory;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SolutionCategoryAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function signInAsAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));
        $this->actingAs($user);

        return $user;
    }

    public function test_admin_can_create_and_edit_a_flat_category(): void
    {
        $this->signInAsAdmin();
        Livewire::test(CreateSolutionCategory::class)
            ->assertFormFieldDoesNotExist('parent_id')
            ->fillForm([
                'name' => 'Danh mục giải pháp QA',
                'description' => 'Mô tả danh mục QA',
                'body' => '<p>Nội dung danh mục QA</p>',
                'is_active' => true,
                'sort_order' => 7,
            ])->call('create')->assertHasNoFormErrors();

        $category = SolutionCategory::where('name', 'Danh mục giải pháp QA')->firstOrFail();
        $slug = $category->slug;
        $this->assertNotEmpty($slug);
        $this->assertSame(7, $category->sort_order);

        Livewire::test(EditSolutionCategory::class, ['record' => $category->id])
            ->fillForm(['name' => 'Danh mục đã sửa QA', 'is_active' => false])
            ->call('save')->assertHasNoFormErrors();
        $this->assertSame('Danh mục đã sửa QA', $category->fresh()->name);
        $this->assertFalse($category->fresh()->is_active);
        $this->assertSame($slug, $category->fresh()->slug);
    }

    public function test_order_is_manual_not_max_plus_one(): void
    {
        SolutionCategory::create(['name' => 'Mốc thứ tự QA', 'sort_order' => 99]);
        $first = SolutionCategory::create(['name' => 'Thứ tự mặc định QA']);
        $second = SolutionCategory::create(['name' => 'Thứ tự mặc định khác QA']);
        $this->assertSame(0, $first->fresh()->sort_order);
        $this->assertSame(0, $second->fresh()->sort_order);

        $first->solutions()->create(['title' => 'Mốc giải pháp QA', 'sort_order' => 99]);
        $solution = $first->solutions()->create(['title' => 'Giải pháp chưa sắp QA']);
        $this->assertSame(0, $solution->fresh()->sort_order);
    }

    public function test_category_with_solutions_cannot_be_deleted(): void
    {
        $this->signInAsAdmin();
        $category = SolutionCategory::create(['name' => 'Có bài QA']);
        $solution = $category->solutions()->create(['title' => 'Không được mất QA']);
        Livewire::test(ListSolutionCategories::class)
            ->assertTableActionDisabled(DeleteAction::class, $category);

        try {
            $category->delete();
            $this->fail('Deleting a populated category must be rejected.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('solution_category_id', $exception->errors());
        }
        $this->assertModelExists($category);
        $this->assertModelExists($solution);
    }

    public function test_deleting_an_empty_category_removes_only_its_slug(): void
    {
        $category = SolutionCategory::create(['name' => 'Xóa danh mục trống QA']);
        $other = SolutionCategory::create(['name' => 'Giữ danh mục QA']);
        $slug = $category->slug;
        $category->delete();

        $this->assertDatabaseMissing('slugs', ['slug' => $slug]);
        $this->assertDatabaseHas('slugs', ['slug' => $other->slug]);
    }

    public function test_users_without_permissions_cannot_access_category_admin(): void
    {
        $category = SolutionCategory::create(['name' => 'Danh mục riêng QA']);
        $this->actingAs(User::factory()->create());
        $this->get('/admin/solution-categories')->assertForbidden();
        $this->get('/admin/solution-categories/create')->assertForbidden();
        $this->get('/admin/solution-categories/'.$category->id.'/edit')->assertForbidden();
    }

    public function test_category_view_permission_does_not_grant_mutations(): void
    {
        $user = User::factory()->create();
        Permission::findOrCreate('ViewAny:SolutionCategory', 'web');
        Permission::findOrCreate('View:SolutionCategory', 'web');
        $user->givePermissionTo(['ViewAny:SolutionCategory', 'View:SolutionCategory']);
        $category = SolutionCategory::create(['name' => 'Chỉ xem QA']);
        $this->actingAs($user);

        $this->assertTrue(Gate::allows('viewAny', SolutionCategory::class));
        $this->assertFalse(Gate::allows('update', $category));
        $this->assertFalse(Gate::allows('delete', $category));
        $this->assertFalse(Gate::allows('reorder', SolutionCategory::class));
        $this->get('/admin/solution-categories/'.$category->id.'/edit')->assertForbidden();
    }

}
