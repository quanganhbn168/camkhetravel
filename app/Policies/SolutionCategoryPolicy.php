<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\SolutionCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SolutionCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $user): bool
    {
        return $user->can('ViewAny:SolutionCategory');
    }

    public function view(AuthUser $user, SolutionCategory $category): bool
    {
        return $user->can('View:SolutionCategory');
    }

    public function create(AuthUser $user): bool
    {
        return $user->can('Create:SolutionCategory');
    }

    public function update(AuthUser $user, SolutionCategory $category): bool
    {
        return $user->can('Update:SolutionCategory');
    }

    public function delete(AuthUser $user, SolutionCategory $category): bool
    {
        return $user->can('Delete:SolutionCategory') && ! $category->solutions()->exists();
    }

    public function reorder(AuthUser $user): bool
    {
        return $user->can('Reorder:SolutionCategory');
    }

    public function replicate(AuthUser $user, SolutionCategory $category): bool
    {
        return $user->can('Replicate:SolutionCategory');
    }
}
