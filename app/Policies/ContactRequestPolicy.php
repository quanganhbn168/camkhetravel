<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ContactRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactRequestPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ContactRequest');
    }

    public function view(AuthUser $authUser, ContactRequest $contactRequest): bool
    {
        return $authUser->can('View:ContactRequest');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ContactRequest');
    }

    public function update(AuthUser $authUser, ContactRequest $contactRequest): bool
    {
        return $authUser->can('Update:ContactRequest');
    }

    public function delete(AuthUser $authUser, ContactRequest $contactRequest): bool
    {
        return $authUser->can('Delete:ContactRequest');
    }

    public function restore(AuthUser $authUser, ContactRequest $contactRequest): bool
    {
        return $authUser->can('Restore:ContactRequest');
    }

    public function forceDelete(AuthUser $authUser, ContactRequest $contactRequest): bool
    {
        return $authUser->can('ForceDelete:ContactRequest');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ContactRequest');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ContactRequest');
    }

    public function replicate(AuthUser $authUser, ContactRequest $contactRequest): bool
    {
        return $authUser->can('Replicate:ContactRequest');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ContactRequest');
    }

}