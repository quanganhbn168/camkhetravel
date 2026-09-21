<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $user = User::query()->firstOrCreate(
            ['email' => 'admin@example.test'],
            ['name' => 'Quản trị viên', 'password' => Hash::make('password')],
        );

        $role = Role::query()->where('name', 'super_admin')->where('guard_name', 'web')->first();

        if ($role !== null) {
            $user->assignRole($role);
        }
    }
}
