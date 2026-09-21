<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    private const ACTIONS = [
        'ViewAny', 'View', 'Create', 'Update', 'Delete', 'Restore',
        'ForceDelete', 'ForceDeleteAny', 'RestoreAny', 'Replicate', 'Reorder',
    ];

    private const RESOURCES = [
        'AboutDepartment', 'Comment', 'ContactRequest', 'Faq', 'HeroSlide',
        'Intro', 'Language', 'Menu', 'Partner', 'PostCategory', 'Post',
        'ProductCategory', 'Product', 'ProjectCategory', 'Project', 'Redirect',
        'ServiceCategory', 'Service', 'Tag', 'Testimonial', 'Media', 'Role',
        'AdminDashboard', 'ManageAboutSettings', 'ManageHomepageSettings',
        'ManageSettings', 'ManageWebsiteSettings', 'WebsiteStatsOverview',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(self::RESOURCES)
            ->flatMap(fn (string $resource): array => array_map(
                fn (string $action): string => $action.':'.$resource,
                self::ACTIONS,
            ))
            ->map(fn (string $name): Permission => Permission::findOrCreate($name, 'web'));

        Role::findOrCreate('super_admin', 'web')->syncPermissions($permissions);
    }
};
