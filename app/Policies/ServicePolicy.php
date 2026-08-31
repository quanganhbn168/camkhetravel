<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Service;

class ServicePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $user): bool { return $user->can('ViewAny:Service'); }
    public function view(AuthUser $user, Service $service): bool { return $user->can('View:Service'); }
    public function create(AuthUser $user): bool { return $user->can('Create:Service'); }
    public function update(AuthUser $user, Service $service): bool { return $user->can('Update:Service'); }
    public function delete(AuthUser $user, Service $service): bool { return $user->can('Delete:Service'); }
    public function restore(AuthUser $user, Service $service): bool { return $user->can('Restore:Service'); }
    public function forceDelete(AuthUser $user, Service $service): bool { return $user->can('ForceDelete:Service'); }
    public function forceDeleteAny(AuthUser $user): bool { return $user->can('ForceDeleteAny:Service'); }
    public function restoreAny(AuthUser $user): bool { return $user->can('RestoreAny:Service'); }
    public function replicate(AuthUser $user, Service $service): bool { return $user->can('Replicate:Service'); }
    public function reorder(AuthUser $user): bool { return $user->can('Reorder:Service'); }
}
