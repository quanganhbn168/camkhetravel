<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $tenants = '[]';
        $users = '[]';
        $userTenantPivot = '[]';
        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":[]}]';
        $directPermissions = '[{"name":"ViewAny:AboutDepartment","guard_name":"web"},{"name":"View:AboutDepartment","guard_name":"web"},{"name":"Create:AboutDepartment","guard_name":"web"},{"name":"Update:AboutDepartment","guard_name":"web"},{"name":"Delete:AboutDepartment","guard_name":"web"},{"name":"Restore:AboutDepartment","guard_name":"web"},{"name":"ForceDelete:AboutDepartment","guard_name":"web"},{"name":"ForceDeleteAny:AboutDepartment","guard_name":"web"},{"name":"RestoreAny:AboutDepartment","guard_name":"web"},{"name":"Replicate:AboutDepartment","guard_name":"web"},{"name":"Reorder:AboutDepartment","guard_name":"web"},{"name":"ViewAny:Comment","guard_name":"web"},{"name":"View:Comment","guard_name":"web"},{"name":"Create:Comment","guard_name":"web"},{"name":"Update:Comment","guard_name":"web"},{"name":"Delete:Comment","guard_name":"web"},{"name":"Restore:Comment","guard_name":"web"},{"name":"ForceDelete:Comment","guard_name":"web"},{"name":"ForceDeleteAny:Comment","guard_name":"web"},{"name":"RestoreAny:Comment","guard_name":"web"},{"name":"Replicate:Comment","guard_name":"web"},{"name":"Reorder:Comment","guard_name":"web"},{"name":"ViewAny:ContactRequest","guard_name":"web"},{"name":"View:ContactRequest","guard_name":"web"},{"name":"Create:ContactRequest","guard_name":"web"},{"name":"Update:ContactRequest","guard_name":"web"},{"name":"Delete:ContactRequest","guard_name":"web"},{"name":"Restore:ContactRequest","guard_name":"web"},{"name":"ForceDelete:ContactRequest","guard_name":"web"},{"name":"ForceDeleteAny:ContactRequest","guard_name":"web"},{"name":"RestoreAny:ContactRequest","guard_name":"web"},{"name":"Replicate:ContactRequest","guard_name":"web"},{"name":"Reorder:ContactRequest","guard_name":"web"},{"name":"ViewAny:Faq","guard_name":"web"},{"name":"View:Faq","guard_name":"web"},{"name":"Create:Faq","guard_name":"web"},{"name":"Update:Faq","guard_name":"web"},{"name":"Delete:Faq","guard_name":"web"},{"name":"Restore:Faq","guard_name":"web"},{"name":"ForceDelete:Faq","guard_name":"web"},{"name":"ForceDeleteAny:Faq","guard_name":"web"},{"name":"RestoreAny:Faq","guard_name":"web"},{"name":"Replicate:Faq","guard_name":"web"},{"name":"Reorder:Faq","guard_name":"web"},{"name":"ViewAny:HeroSlide","guard_name":"web"},{"name":"View:HeroSlide","guard_name":"web"},{"name":"Create:HeroSlide","guard_name":"web"},{"name":"Update:HeroSlide","guard_name":"web"},{"name":"Delete:HeroSlide","guard_name":"web"},{"name":"Restore:HeroSlide","guard_name":"web"},{"name":"ForceDelete:HeroSlide","guard_name":"web"},{"name":"ForceDeleteAny:HeroSlide","guard_name":"web"},{"name":"RestoreAny:HeroSlide","guard_name":"web"},{"name":"Replicate:HeroSlide","guard_name":"web"},{"name":"Reorder:HeroSlide","guard_name":"web"},{"name":"ViewAny:Intro","guard_name":"web"},{"name":"View:Intro","guard_name":"web"},{"name":"Create:Intro","guard_name":"web"},{"name":"Update:Intro","guard_name":"web"},{"name":"Delete:Intro","guard_name":"web"},{"name":"Restore:Intro","guard_name":"web"},{"name":"ForceDelete:Intro","guard_name":"web"},{"name":"ForceDeleteAny:Intro","guard_name":"web"},{"name":"RestoreAny:Intro","guard_name":"web"},{"name":"Replicate:Intro","guard_name":"web"},{"name":"Reorder:Intro","guard_name":"web"},{"name":"ViewAny:LandingPage","guard_name":"web"},{"name":"View:LandingPage","guard_name":"web"},{"name":"Create:LandingPage","guard_name":"web"},{"name":"Update:LandingPage","guard_name":"web"},{"name":"Delete:LandingPage","guard_name":"web"},{"name":"Restore:LandingPage","guard_name":"web"},{"name":"ForceDelete:LandingPage","guard_name":"web"},{"name":"ForceDeleteAny:LandingPage","guard_name":"web"},{"name":"RestoreAny:LandingPage","guard_name":"web"},{"name":"Replicate:LandingPage","guard_name":"web"},{"name":"Reorder:LandingPage","guard_name":"web"},{"name":"ViewAny:Language","guard_name":"web"},{"name":"View:Language","guard_name":"web"},{"name":"Create:Language","guard_name":"web"},{"name":"Update:Language","guard_name":"web"},{"name":"Delete:Language","guard_name":"web"},{"name":"Restore:Language","guard_name":"web"},{"name":"ForceDelete:Language","guard_name":"web"},{"name":"ForceDeleteAny:Language","guard_name":"web"},{"name":"RestoreAny:Language","guard_name":"web"},{"name":"Replicate:Language","guard_name":"web"},{"name":"Reorder:Language","guard_name":"web"},{"name":"ViewAny:Menu","guard_name":"web"},{"name":"View:Menu","guard_name":"web"},{"name":"Create:Menu","guard_name":"web"},{"name":"Update:Menu","guard_name":"web"},{"name":"Delete:Menu","guard_name":"web"},{"name":"Restore:Menu","guard_name":"web"},{"name":"ForceDelete:Menu","guard_name":"web"},{"name":"ForceDeleteAny:Menu","guard_name":"web"},{"name":"RestoreAny:Menu","guard_name":"web"},{"name":"Replicate:Menu","guard_name":"web"},{"name":"Reorder:Menu","guard_name":"web"},{"name":"ViewAny:Partner","guard_name":"web"},{"name":"View:Partner","guard_name":"web"},{"name":"Create:Partner","guard_name":"web"},{"name":"Update:Partner","guard_name":"web"},{"name":"Delete:Partner","guard_name":"web"},{"name":"Restore:Partner","guard_name":"web"},{"name":"ForceDelete:Partner","guard_name":"web"},{"name":"ForceDeleteAny:Partner","guard_name":"web"},{"name":"RestoreAny:Partner","guard_name":"web"},{"name":"Replicate:Partner","guard_name":"web"},{"name":"Reorder:Partner","guard_name":"web"},{"name":"ViewAny:PostCategory","guard_name":"web"},{"name":"View:PostCategory","guard_name":"web"},{"name":"Create:PostCategory","guard_name":"web"},{"name":"Update:PostCategory","guard_name":"web"},{"name":"Delete:PostCategory","guard_name":"web"},{"name":"Restore:PostCategory","guard_name":"web"},{"name":"ForceDelete:PostCategory","guard_name":"web"},{"name":"ForceDeleteAny:PostCategory","guard_name":"web"},{"name":"RestoreAny:PostCategory","guard_name":"web"},{"name":"Replicate:PostCategory","guard_name":"web"},{"name":"Reorder:PostCategory","guard_name":"web"},{"name":"ViewAny:Post","guard_name":"web"},{"name":"View:Post","guard_name":"web"},{"name":"Create:Post","guard_name":"web"},{"name":"Update:Post","guard_name":"web"},{"name":"Delete:Post","guard_name":"web"},{"name":"Restore:Post","guard_name":"web"},{"name":"ForceDelete:Post","guard_name":"web"},{"name":"ForceDeleteAny:Post","guard_name":"web"},{"name":"RestoreAny:Post","guard_name":"web"},{"name":"Replicate:Post","guard_name":"web"},{"name":"Reorder:Post","guard_name":"web"},{"name":"ViewAny:PricingPlan","guard_name":"web"},{"name":"View:PricingPlan","guard_name":"web"},{"name":"Create:PricingPlan","guard_name":"web"},{"name":"Update:PricingPlan","guard_name":"web"},{"name":"Delete:PricingPlan","guard_name":"web"},{"name":"Restore:PricingPlan","guard_name":"web"},{"name":"ForceDelete:PricingPlan","guard_name":"web"},{"name":"ForceDeleteAny:PricingPlan","guard_name":"web"},{"name":"RestoreAny:PricingPlan","guard_name":"web"},{"name":"Replicate:PricingPlan","guard_name":"web"},{"name":"Reorder:PricingPlan","guard_name":"web"},{"name":"ViewAny:ProductCategory","guard_name":"web"},{"name":"View:ProductCategory","guard_name":"web"},{"name":"Create:ProductCategory","guard_name":"web"},{"name":"Update:ProductCategory","guard_name":"web"},{"name":"Delete:ProductCategory","guard_name":"web"},{"name":"Restore:ProductCategory","guard_name":"web"},{"name":"ForceDelete:ProductCategory","guard_name":"web"},{"name":"ForceDeleteAny:ProductCategory","guard_name":"web"},{"name":"RestoreAny:ProductCategory","guard_name":"web"},{"name":"Replicate:ProductCategory","guard_name":"web"},{"name":"Reorder:ProductCategory","guard_name":"web"},{"name":"ViewAny:Product","guard_name":"web"},{"name":"View:Product","guard_name":"web"},{"name":"Create:Product","guard_name":"web"},{"name":"Update:Product","guard_name":"web"},{"name":"Delete:Product","guard_name":"web"},{"name":"Restore:Product","guard_name":"web"},{"name":"ForceDelete:Product","guard_name":"web"},{"name":"ForceDeleteAny:Product","guard_name":"web"},{"name":"RestoreAny:Product","guard_name":"web"},{"name":"Replicate:Product","guard_name":"web"},{"name":"Reorder:Product","guard_name":"web"},{"name":"ViewAny:ProjectCategory","guard_name":"web"},{"name":"View:ProjectCategory","guard_name":"web"},{"name":"Create:ProjectCategory","guard_name":"web"},{"name":"Update:ProjectCategory","guard_name":"web"},{"name":"Delete:ProjectCategory","guard_name":"web"},{"name":"Restore:ProjectCategory","guard_name":"web"},{"name":"ForceDelete:ProjectCategory","guard_name":"web"},{"name":"ForceDeleteAny:ProjectCategory","guard_name":"web"},{"name":"RestoreAny:ProjectCategory","guard_name":"web"},{"name":"Replicate:ProjectCategory","guard_name":"web"},{"name":"Reorder:ProjectCategory","guard_name":"web"},{"name":"ViewAny:Project","guard_name":"web"},{"name":"View:Project","guard_name":"web"},{"name":"Create:Project","guard_name":"web"},{"name":"Update:Project","guard_name":"web"},{"name":"Delete:Project","guard_name":"web"},{"name":"Restore:Project","guard_name":"web"},{"name":"ForceDelete:Project","guard_name":"web"},{"name":"ForceDeleteAny:Project","guard_name":"web"},{"name":"RestoreAny:Project","guard_name":"web"},{"name":"Replicate:Project","guard_name":"web"},{"name":"Reorder:Project","guard_name":"web"},{"name":"ViewAny:Redirect","guard_name":"web"},{"name":"View:Redirect","guard_name":"web"},{"name":"Create:Redirect","guard_name":"web"},{"name":"Update:Redirect","guard_name":"web"},{"name":"Delete:Redirect","guard_name":"web"},{"name":"Restore:Redirect","guard_name":"web"},{"name":"ForceDelete:Redirect","guard_name":"web"},{"name":"ForceDeleteAny:Redirect","guard_name":"web"},{"name":"RestoreAny:Redirect","guard_name":"web"},{"name":"Replicate:Redirect","guard_name":"web"},{"name":"Reorder:Redirect","guard_name":"web"},{"name":"ViewAny:ServiceCategory","guard_name":"web"},{"name":"View:ServiceCategory","guard_name":"web"},{"name":"Create:ServiceCategory","guard_name":"web"},{"name":"Update:ServiceCategory","guard_name":"web"},{"name":"Delete:ServiceCategory","guard_name":"web"},{"name":"Restore:ServiceCategory","guard_name":"web"},{"name":"ForceDelete:ServiceCategory","guard_name":"web"},{"name":"ForceDeleteAny:ServiceCategory","guard_name":"web"},{"name":"RestoreAny:ServiceCategory","guard_name":"web"},{"name":"Replicate:ServiceCategory","guard_name":"web"},{"name":"Reorder:ServiceCategory","guard_name":"web"},{"name":"ViewAny:ServicePricing","guard_name":"web"},{"name":"View:ServicePricing","guard_name":"web"},{"name":"Create:ServicePricing","guard_name":"web"},{"name":"Update:ServicePricing","guard_name":"web"},{"name":"Delete:ServicePricing","guard_name":"web"},{"name":"Restore:ServicePricing","guard_name":"web"},{"name":"ForceDelete:ServicePricing","guard_name":"web"},{"name":"ForceDeleteAny:ServicePricing","guard_name":"web"},{"name":"RestoreAny:ServicePricing","guard_name":"web"},{"name":"Replicate:ServicePricing","guard_name":"web"},{"name":"Reorder:ServicePricing","guard_name":"web"},{"name":"ViewAny:Service","guard_name":"web"},{"name":"View:Service","guard_name":"web"},{"name":"Create:Service","guard_name":"web"},{"name":"Update:Service","guard_name":"web"},{"name":"Delete:Service","guard_name":"web"},{"name":"Restore:Service","guard_name":"web"},{"name":"ForceDelete:Service","guard_name":"web"},{"name":"ForceDeleteAny:Service","guard_name":"web"},{"name":"RestoreAny:Service","guard_name":"web"},{"name":"Replicate:Service","guard_name":"web"},{"name":"Reorder:Service","guard_name":"web"},{"name":"ViewAny:Tag","guard_name":"web"},{"name":"View:Tag","guard_name":"web"},{"name":"Create:Tag","guard_name":"web"},{"name":"Update:Tag","guard_name":"web"},{"name":"Delete:Tag","guard_name":"web"},{"name":"Restore:Tag","guard_name":"web"},{"name":"ForceDelete:Tag","guard_name":"web"},{"name":"ForceDeleteAny:Tag","guard_name":"web"},{"name":"RestoreAny:Tag","guard_name":"web"},{"name":"Replicate:Tag","guard_name":"web"},{"name":"Reorder:Tag","guard_name":"web"},{"name":"ViewAny:Testimonial","guard_name":"web"},{"name":"View:Testimonial","guard_name":"web"},{"name":"Create:Testimonial","guard_name":"web"},{"name":"Update:Testimonial","guard_name":"web"},{"name":"Delete:Testimonial","guard_name":"web"},{"name":"Restore:Testimonial","guard_name":"web"},{"name":"ForceDelete:Testimonial","guard_name":"web"},{"name":"ForceDeleteAny:Testimonial","guard_name":"web"},{"name":"RestoreAny:Testimonial","guard_name":"web"},{"name":"Replicate:Testimonial","guard_name":"web"},{"name":"Reorder:Testimonial","guard_name":"web"},{"name":"ViewAny:Media","guard_name":"web"},{"name":"View:Media","guard_name":"web"},{"name":"Create:Media","guard_name":"web"},{"name":"Update:Media","guard_name":"web"},{"name":"Delete:Media","guard_name":"web"},{"name":"Restore:Media","guard_name":"web"},{"name":"ForceDelete:Media","guard_name":"web"},{"name":"ForceDeleteAny:Media","guard_name":"web"},{"name":"RestoreAny:Media","guard_name":"web"},{"name":"Replicate:Media","guard_name":"web"},{"name":"Reorder:Media","guard_name":"web"},{"name":"ViewAny:Role","guard_name":"web"},{"name":"View:Role","guard_name":"web"},{"name":"Create:Role","guard_name":"web"},{"name":"Update:Role","guard_name":"web"},{"name":"Delete:Role","guard_name":"web"},{"name":"Restore:Role","guard_name":"web"},{"name":"ForceDelete:Role","guard_name":"web"},{"name":"ForceDeleteAny:Role","guard_name":"web"},{"name":"RestoreAny:Role","guard_name":"web"},{"name":"Replicate:Role","guard_name":"web"},{"name":"Reorder:Role","guard_name":"web"},{"name":"View:AdminDashboard","guard_name":"web"},{"name":"View:ManageAboutSettings","guard_name":"web"},{"name":"View:ManageHomepageSettings","guard_name":"web"},{"name":"View:ManageSettings","guard_name":"web"},{"name":"View:ManageWebsiteSettings","guard_name":"web"},{"name":"View:WebsiteStatsOverview","guard_name":"web"}]';

        // 1. Seed tenants first (if present)
        if (! blank($tenants) && $tenants !== '[]') {
            static::seedTenants($tenants);
        }

        // 2. Seed roles with permissions
        static::makeRolesWithPermissions($rolesWithPermissions);

        // 3. Seed direct permissions
        static::makeDirectPermissions($directPermissions);

        // 4. Seed users with their roles/permissions (if present)
        if (! blank($users) && $users !== '[]') {
            static::seedUsers($users);
        }

        // 5. Seed user-tenant pivot (if present)
        if (! blank($userTenantPivot) && $userTenantPivot !== '[]') {
            static::seedUserTenantPivot($userTenantPivot);
        }

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function seedTenants(string $tenants): void
    {
        if (blank($tenantData = json_decode($tenants, true))) {
            return;
        }

        $tenantModel = '';
        if (blank($tenantModel)) {
            return;
        }

        foreach ($tenantData as $tenant) {
            $tenantModel::firstOrCreate(
                ['id' => $tenant['id']],
                $tenant
            );
        }
    }

    protected static function seedUsers(string $users): void
    {
        if (blank($userData = json_decode($users, true))) {
            return;
        }

        $userModel = 'App\Models\User';
        $tenancyEnabled = false;

        foreach ($userData as $data) {
            // Extract role/permission data before creating user
            $roles = $data['roles'] ?? [];
            $permissions = $data['permissions'] ?? [];
            $tenantRoles = $data['tenant_roles'] ?? [];
            $tenantPermissions = $data['tenant_permissions'] ?? [];
            unset($data['roles'], $data['permissions'], $data['tenant_roles'], $data['tenant_permissions']);

            $user = $userModel::firstOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Handle tenancy mode - sync roles/permissions per tenant
            if ($tenancyEnabled && (! empty($tenantRoles) || ! empty($tenantPermissions))) {
                foreach ($tenantRoles as $tenantId => $roleNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncRoles($roleNames);
                }

                foreach ($tenantPermissions as $tenantId => $permissionNames) {
                    $contextId = $tenantId === '_global' ? null : $tenantId;
                    setPermissionsTeamId($contextId);
                    $user->syncPermissions($permissionNames);
                }
            } else {
                // Non-tenancy mode
                if (! empty($roles)) {
                    $user->syncRoles($roles);
                }

                if (! empty($permissions)) {
                    $user->syncPermissions($permissions);
                }
            }
        }
    }

    protected static function seedUserTenantPivot(string $pivot): void
    {
        if (blank($pivotData = json_decode($pivot, true))) {
            return;
        }

        $pivotTable = '';
        if (blank($pivotTable)) {
            return;
        }

        foreach ($pivotData as $row) {
            $uniqueKeys = [];

            if (isset($row['user_id'])) {
                $uniqueKeys['user_id'] = $row['user_id'];
            }

            $tenantForeignKey = 'team_id';
            if (! blank($tenantForeignKey) && isset($row[$tenantForeignKey])) {
                $uniqueKeys[$tenantForeignKey] = $row[$tenantForeignKey];
            }

            if (! empty($uniqueKeys)) {
                DB::table($pivotTable)->updateOrInsert($uniqueKeys, $row);
            }
        }
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            return;
        }

        /** @var Model $roleModel */
        $roleModel = Utils::getRoleModel();
        /** @var Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        $tenancyEnabled = false;
        $teamForeignKey = 'team_id';

        foreach ($rolePlusPermissions as $rolePlusPermission) {
            $tenantId = $rolePlusPermission[$teamForeignKey] ?? null;

            // Set tenant context for role creation and permission sync
            if ($tenancyEnabled) {
                setPermissionsTeamId($tenantId);
            }

            $roleData = [
                'name' => $rolePlusPermission['name'],
                'guard_name' => $rolePlusPermission['guard_name'],
            ];

            // Include tenant ID in role data (can be null for global roles)
            if ($tenancyEnabled && ! blank($teamForeignKey)) {
                $roleData[$teamForeignKey] = $tenantId;
            }

            $role = $roleModel::firstOrCreate($roleData);

            if (! blank($rolePlusPermission['permissions'])) {
                $permissionModels = collect($rolePlusPermission['permissions'])
                    ->map(fn ($permission) => $permissionModel::firstOrCreate([
                        'name' => $permission,
                        'guard_name' => $rolePlusPermission['guard_name'],
                    ]))
                    ->all();

                $role->syncPermissions($permissionModels);
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (blank($permissions = json_decode($directPermissions, true))) {
            return;
        }

        /** @var Model $permissionModel */
        $permissionModel = Utils::getPermissionModel();

        foreach ($permissions as $permission) {
            if ($permissionModel::whereName($permission['name'])->doesntExist()) {
                $permissionModel::create([
                    'name' => $permission['name'],
                    'guard_name' => $permission['guard_name'],
                ]);
            }
        }
    }
}
