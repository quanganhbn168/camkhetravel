<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServicePricing;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ServicePricingPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ServicePricing');
    }

    public function view(AuthUser $authUser, ServicePricing $servicePricing): bool
    {
        return $authUser->can('View:ServicePricing');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ServicePricing');
    }

    public function update(AuthUser $authUser, ServicePricing $servicePricing): bool
    {
        return $authUser->can('Update:ServicePricing');
    }

    public function delete(AuthUser $authUser, ServicePricing $servicePricing): bool
    {
        return $authUser->can('Delete:ServicePricing');
    }

    public function restore(AuthUser $authUser, ServicePricing $servicePricing): bool
    {
        return $authUser->can('Restore:ServicePricing');
    }

    public function forceDelete(AuthUser $authUser, ServicePricing $servicePricing): bool
    {
        return $authUser->can('ForceDelete:ServicePricing');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ServicePricing');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ServicePricing');
    }

    public function replicate(AuthUser $authUser, ServicePricing $servicePricing): bool
    {
        return $authUser->can('Replicate:ServicePricing');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ServicePricing');
    }
}
