<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_groups_render_for_a_user_with_full_access(): void
    {
        Gate::before(fn () => true);

        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super_admin'));

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Dịch vụ & giải pháp')
            ->assertSee('Cài đặt website')
            ->assertSee('Trang');
    }
}
