<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\HeroSlide;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class HeroSlidePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HeroSlide');
    }

    public function view(AuthUser $authUser, HeroSlide $heroSlide): bool
    {
        return $authUser->can('View:HeroSlide');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HeroSlide');
    }

    public function update(AuthUser $authUser, HeroSlide $heroSlide): bool
    {
        return $authUser->can('Update:HeroSlide');
    }

    public function delete(AuthUser $authUser, HeroSlide $heroSlide): bool
    {
        return $authUser->can('Delete:HeroSlide');
    }

    public function restore(AuthUser $authUser, HeroSlide $heroSlide): bool
    {
        return $authUser->can('Restore:HeroSlide');
    }

    public function forceDelete(AuthUser $authUser, HeroSlide $heroSlide): bool
    {
        return $authUser->can('ForceDelete:HeroSlide');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HeroSlide');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HeroSlide');
    }

    public function replicate(AuthUser $authUser, HeroSlide $heroSlide): bool
    {
        return $authUser->can('Replicate:HeroSlide');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HeroSlide');
    }
}
