<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LandingCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class LandingCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:ServiceCategory'); }
    public function view(AuthUser $authUser, LandingCategory $category): bool { return $authUser->can('View:ServiceCategory'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:ServiceCategory'); }
    public function update(AuthUser $authUser, LandingCategory $category): bool { return $authUser->can('Update:ServiceCategory'); }
    public function delete(AuthUser $authUser, LandingCategory $category): bool { return $authUser->can('Delete:ServiceCategory'); }
    public function restore(AuthUser $authUser, LandingCategory $category): bool { return $authUser->can('Restore:ServiceCategory'); }
    public function forceDelete(AuthUser $authUser, LandingCategory $category): bool { return $authUser->can('ForceDelete:ServiceCategory'); }
    public function forceDeleteAny(AuthUser $authUser): bool { return $authUser->can('ForceDeleteAny:ServiceCategory'); }
    public function restoreAny(AuthUser $authUser): bool { return $authUser->can('RestoreAny:ServiceCategory'); }
    public function replicate(AuthUser $authUser, LandingCategory $category): bool { return $authUser->can('Replicate:ServiceCategory'); }
    public function reorder(AuthUser $authUser): bool { return $authUser->can('Reorder:ServiceCategory'); }
}
