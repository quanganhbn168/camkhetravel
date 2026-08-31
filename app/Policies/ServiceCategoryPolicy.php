<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServiceCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ServiceCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $user): bool { return $user->can('ViewAny:ServiceCategory'); }
    public function view(AuthUser $user, ServiceCategory $category): bool { return $user->can('View:ServiceCategory'); }
    public function create(AuthUser $user): bool { return $user->can('Create:ServiceCategory'); }
    public function update(AuthUser $user, ServiceCategory $category): bool { return $user->can('Update:ServiceCategory'); }
    public function delete(AuthUser $user, ServiceCategory $category): bool { return $user->can('Delete:ServiceCategory'); }
    public function restore(AuthUser $user, ServiceCategory $category): bool { return $user->can('Restore:ServiceCategory'); }
    public function forceDelete(AuthUser $user, ServiceCategory $category): bool { return $user->can('ForceDelete:ServiceCategory'); }
    public function forceDeleteAny(AuthUser $user): bool { return $user->can('ForceDeleteAny:ServiceCategory'); }
    public function restoreAny(AuthUser $user): bool { return $user->can('RestoreAny:ServiceCategory'); }
    public function replicate(AuthUser $user, ServiceCategory $category): bool { return $user->can('Replicate:ServiceCategory'); }
    public function reorder(AuthUser $user): bool { return $user->can('Reorder:ServiceCategory'); }
}
