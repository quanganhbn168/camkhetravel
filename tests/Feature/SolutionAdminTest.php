<?php

namespace Tests\Feature;

use App\Filament\Resources\Solutions\Pages\CreateSolution;
use App\Filament\Resources\Solutions\Pages\EditSolution;
use App\Models\Solution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SolutionAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_edit_solution(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));
        $this->actingAs($user);
        Livewire::test(CreateSolution::class)->fillForm([
            'title' => 'Giải pháp quản trị QA', 'short_title' => 'Nhà máy QA',
            'excerpt' => 'Mô tả QA', 'body' => '<p>Nội dung QA</p>',
            'is_active' => true, 'is_home' => true, 'sort_order' => 3,
        ])->call('create')->assertHasNoFormErrors();
        $solution = Solution::where('title', 'Giải pháp quản trị QA')->firstOrFail();
        $this->assertNotEmpty($solution->slug);
        Livewire::test(EditSolution::class, ['record' => $solution->id])->fillForm(['is_home' => false])->call('save')->assertHasNoFormErrors();
        $this->assertFalse($solution->fresh()->is_home);
        $this->get(route('solutions.show', ['solution' => $solution->slug]))->assertOk()->assertSee('Nội dung QA');
    }

    public function test_user_without_permission_cannot_edit_solution(): void
    {
        $solution = Solution::create(['title' => 'Giải pháp riêng QA']);
        $this->actingAs(User::factory()->create());
        $this->get('/admin/solutions')->assertForbidden();
        $this->get('/admin/solutions/'.$solution->id.'/edit')->assertForbidden();
    }
}
