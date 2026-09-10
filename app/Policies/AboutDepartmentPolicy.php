<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AboutDepartment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class AboutDepartmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AboutDepartment');
    }

    public function view(AuthUser $authUser, AboutDepartment $aboutDepartment): bool
    {
        return $authUser->can('View:AboutDepartment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AboutDepartment');
    }

    public function update(AuthUser $authUser, AboutDepartment $aboutDepartment): bool
    {
        return $authUser->can('Update:AboutDepartment');
    }

    public function delete(AuthUser $authUser, AboutDepartment $aboutDepartment): bool
    {
        return $authUser->can('Delete:AboutDepartment');
    }

    public function restore(AuthUser $authUser, AboutDepartment $aboutDepartment): bool
    {
        return $authUser->can('Restore:AboutDepartment');
    }

    public function forceDelete(AuthUser $authUser, AboutDepartment $aboutDepartment): bool
    {
        return $authUser->can('ForceDelete:AboutDepartment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AboutDepartment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AboutDepartment');
    }

    public function replicate(AuthUser $authUser, AboutDepartment $aboutDepartment): bool
    {
        return $authUser->can('Replicate:AboutDepartment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AboutDepartment');
    }
}
