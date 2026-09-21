<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\ShieldSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_is_a_simple_content_management_start_page(): void
    {
        $this->seed([ShieldSeeder::class, AdminUserSeeder::class]);
        $user = User::query()->where('email', 'admin@example.test')->firstOrFail();

        $this->actingAs($user)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Truy cập nhanh')
            ->assertSee('Dịch vụ')
            ->assertSee('Dự án')
            ->assertSee('Sản phẩm')
            ->assertSee('Bài viết');
    }
}
