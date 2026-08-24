<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PricingPlan;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PricingPlanPolicy
{
    use HandlesAuthorization;

    public function before(AuthUser $authUser): ?bool
    {
        return $authUser->hasRole('super_admin') ? true : null;
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PricingPlan');
    }

    public function view(AuthUser $authUser, PricingPlan $pricingPlan): bool
    {
        return $authUser->can('View:PricingPlan');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PricingPlan');
    }

    public function update(AuthUser $authUser, PricingPlan $pricingPlan): bool
    {
        return $authUser->can('Update:PricingPlan');
    }

    public function delete(AuthUser $authUser, PricingPlan $pricingPlan): bool
    {
        return $authUser->can('Delete:PricingPlan');
    }

    public function restore(AuthUser $authUser, PricingPlan $pricingPlan): bool
    {
        return $authUser->can('Restore:PricingPlan');
    }

    public function forceDelete(AuthUser $authUser, PricingPlan $pricingPlan): bool
    {
        return $authUser->can('ForceDelete:PricingPlan');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PricingPlan');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PricingPlan');
    }

    public function replicate(AuthUser $authUser, PricingPlan $pricingPlan): bool
    {
        return $authUser->can('Replicate:PricingPlan');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PricingPlan');
    }
}
