<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Intro;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class IntroPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Intro');
    }

    public function view(AuthUser $authUser, Intro $intro): bool
    {
        return $authUser->can('View:Intro');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Intro');
    }

    public function update(AuthUser $authUser, Intro $intro): bool
    {
        return $authUser->can('Update:Intro');
    }

    public function delete(AuthUser $authUser, Intro $intro): bool
    {
        return $authUser->can('Delete:Intro');
    }

    public function restore(AuthUser $authUser, Intro $intro): bool
    {
        return $authUser->can('Restore:Intro');
    }

    public function forceDelete(AuthUser $authUser, Intro $intro): bool
    {
        return $authUser->can('ForceDelete:Intro');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Intro');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Intro');
    }

    public function replicate(AuthUser $authUser, Intro $intro): bool
    {
        return $authUser->can('Replicate:Intro');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Intro');
    }
}
