<?php

namespace Tests\Feature;

use App\Filament\Resources\Solutions\Pages\CreateSolution;
use App\Filament\Resources\Solutions\Pages\EditSolution;
use App\Models\Solution;
use App\Models\SolutionCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SolutionAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        SolutionCategory::create(['seed_key' => 'pccc', 'name' => 'Nhóm giải pháp thứ nhất', 'is_active' => true]);
        SolutionCategory::create(['seed_key' => 'hvac', 'name' => 'Nhóm giải pháp thứ hai', 'is_active' => true]);
    }

    private function signInAsAdmin(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin', 'web'));
        $this->actingAs($user);
    }

    public function test_admin_can_create_and_edit_solution(): void
    {
        $this->signInAsAdmin();
        $category = SolutionCategory::where('seed_key', 'pccc')->firstOrFail();
        Livewire::test(CreateSolution::class)
            ->assertFormFieldDoesNotExist('short_title')
            ->fillForm([
                'solution_category_id' => $category->id,
                'title' => 'Giải pháp PCCC cho nhà máy QA',
                'excerpt' => 'Mô tả QA', 'body' => '<p>Nội dung QA</p>',
                'is_active' => true, 'is_home' => true, 'sort_order' => 3,
            ])->call('create')->assertHasNoFormErrors();
        $solution = Solution::where('title', 'Giải pháp PCCC cho nhà máy QA')->firstOrFail();
        $slug = $solution->slug;
        $this->assertNotEmpty($slug);
        $this->assertSame($category->id, $solution->solution_category_id);
        $this->assertSame(3, $solution->sort_order);

        $other = SolutionCategory::where('seed_key', 'hvac')->firstOrFail();
        Livewire::test(EditSolution::class, ['record' => $solution->id])
            ->fillForm(['solution_category_id' => $other->id, 'is_home' => false])
            ->call('save')->assertHasNoFormErrors();
        $this->assertFalse($solution->fresh()->is_home);
        $this->assertSame($other->id, $solution->fresh()->solution_category_id);
        $this->assertSame($slug, $solution->fresh()->slug);
        $this->assertSame(0, $category->solutions()->count());
        $this->assertSame(1, $other->solutions()->count());
        $this->get(route('solutions.show', ['solution' => $slug]))->assertOk()->assertSee('Nội dung QA');
    }

    public function test_category_is_required_and_must_exist(): void
    {
        $this->signInAsAdmin();
        Livewire::test(CreateSolution::class)
            ->fillForm(['title' => 'Thiếu danh mục QA', 'solution_category_id' => null])
            ->call('create')->assertHasFormErrors(['solution_category_id' => 'required']);
        Livewire::test(CreateSolution::class)
            ->fillForm(['title' => 'Danh mục sai QA', 'solution_category_id' => 99999999])
            ->call('create')->assertHasFormErrors(['solution_category_id']);
        $this->assertSame(0, Solution::count());
    }

    public function test_negative_order_is_rejected(): void
    {
        $this->signInAsAdmin();
        Livewire::test(CreateSolution::class)
            ->fillForm([
                'title' => 'Thứ tự sai QA',
                'solution_category_id' => SolutionCategory::firstOrFail()->id,
                'sort_order' => -1,
            ])->call('create')->assertHasFormErrors(['sort_order']);
        $this->assertSame(0, Solution::count());
    }

    public function test_user_without_permission_cannot_edit_solution(): void
    {
        $solution = SolutionCategory::firstOrFail()->solutions()->create(['title' => 'Giải pháp riêng QA']);
        $this->actingAs(User::factory()->create());
        $this->get('/admin/solutions')->assertForbidden();
        $this->get('/admin/solutions/create')->assertForbidden();
        $this->get('/admin/solutions/'.$solution->id.'/edit')->assertForbidden();
    }
}
