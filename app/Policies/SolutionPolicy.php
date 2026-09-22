<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Solution;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SolutionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Solution');
    }

    public function view(AuthUser $authUser, Solution $solution): bool
    {
        return $authUser->can('View:Solution');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Solution');
    }

    public function update(AuthUser $authUser, Solution $solution): bool
    {
        return $authUser->can('Update:Solution');
    }

    public function delete(AuthUser $authUser, Solution $solution): bool
    {
        return $authUser->can('Delete:Solution');
    }

    public function restore(AuthUser $authUser, Solution $solution): bool
    {
        return $authUser->can('Restore:Solution');
    }

    public function forceDelete(AuthUser $authUser, Solution $solution): bool
    {
        return $authUser->can('ForceDelete:Solution');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Solution');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Solution');
    }

    public function replicate(AuthUser $authUser, Solution $solution): bool
    {
        return $authUser->can('Replicate:Solution');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Solution');
    }
}
