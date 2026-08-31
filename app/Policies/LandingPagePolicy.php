<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LandingPage;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class LandingPagePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $user): bool { return $user->can('ViewAny:LandingPage') || $user->can('ViewAny:Service'); }
    public function view(AuthUser $user, LandingPage $landingPage): bool { return $user->can('View:LandingPage') || $user->can('View:Service'); }
    public function create(AuthUser $user): bool { return $user->can('Create:LandingPage') || $user->can('Create:Service'); }
    public function update(AuthUser $user, LandingPage $landingPage): bool { return $user->can('Update:LandingPage') || $user->can('Update:Service'); }
    public function delete(AuthUser $user, LandingPage $landingPage): bool { return $user->can('Delete:LandingPage') || $user->can('Delete:Service'); }
    public function restore(AuthUser $user, LandingPage $landingPage): bool { return $user->can('Restore:LandingPage') || $user->can('Restore:Service'); }
    public function forceDelete(AuthUser $user, LandingPage $landingPage): bool { return $user->can('ForceDelete:LandingPage') || $user->can('ForceDelete:Service'); }
    public function forceDeleteAny(AuthUser $user): bool { return $user->can('ForceDeleteAny:LandingPage') || $user->can('ForceDeleteAny:Service'); }
    public function restoreAny(AuthUser $user): bool { return $user->can('RestoreAny:LandingPage') || $user->can('RestoreAny:Service'); }
    public function replicate(AuthUser $user, LandingPage $landingPage): bool { return $user->can('Replicate:LandingPage') || $user->can('Replicate:Service'); }
    public function reorder(AuthUser $user): bool { return $user->can('Reorder:LandingPage') || $user->can('Reorder:Service'); }
}
