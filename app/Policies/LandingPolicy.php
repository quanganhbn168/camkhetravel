<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Landing;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class LandingPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool { return $authUser->can('ViewAny:Service'); }
    public function view(AuthUser $authUser, Landing $landing): bool { return $authUser->can('View:Service'); }
    public function create(AuthUser $authUser): bool { return $authUser->can('Create:Service'); }
    public function update(AuthUser $authUser, Landing $landing): bool { return $authUser->can('Update:Service'); }
    public function delete(AuthUser $authUser, Landing $landing): bool { return $authUser->can('Delete:Service'); }
    public function restore(AuthUser $authUser, Landing $landing): bool { return $authUser->can('Restore:Service'); }
    public function forceDelete(AuthUser $authUser, Landing $landing): bool { return $authUser->can('ForceDelete:Service'); }
    public function forceDeleteAny(AuthUser $authUser): bool { return $authUser->can('ForceDeleteAny:Service'); }
    public function restoreAny(AuthUser $authUser): bool { return $authUser->can('RestoreAny:Service'); }
    public function replicate(AuthUser $authUser, Landing $landing): bool { return $authUser->can('Replicate:Service'); }
    public function reorder(AuthUser $authUser): bool { return $authUser->can('Reorder:Service'); }
}
