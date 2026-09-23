<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class SolutionPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $admin = Role::findOrCreate('super_admin', 'web');
        $actions = ['ViewAny', 'View', 'Create', 'Update', 'Delete', 'Restore', 'ForceDelete', 'ForceDeleteAny', 'RestoreAny', 'Replicate', 'Reorder'];

        foreach (['SolutionCategory', 'Solution'] as $resource) {
            foreach ($actions as $action) {
                $permission = Permission::findOrCreate($action.':'.$resource, 'web');
                // Add permissions; do not revoke permissions for other modules or roles.
                $admin->givePermissionTo($permission);
            }
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
