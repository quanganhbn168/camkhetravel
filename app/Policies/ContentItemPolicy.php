<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ContentItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContentItemPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ContentItem');
    }

    public function view(AuthUser $authUser, ContentItem $contentItem): bool
    {
        return $authUser->can('View:ContentItem');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ContentItem');
    }

    public function update(AuthUser $authUser, ContentItem $contentItem): bool
    {
        return $authUser->can('Update:ContentItem');
    }

    public function delete(AuthUser $authUser, ContentItem $contentItem): bool
    {
        return $authUser->can('Delete:ContentItem');
    }

    public function restore(AuthUser $authUser, ContentItem $contentItem): bool
    {
        return $authUser->can('Restore:ContentItem');
    }

    public function forceDelete(AuthUser $authUser, ContentItem $contentItem): bool
    {
        return $authUser->can('ForceDelete:ContentItem');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ContentItem');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ContentItem');
    }

    public function replicate(AuthUser $authUser, ContentItem $contentItem): bool
    {
        return $authUser->can('Replicate:ContentItem');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ContentItem');
    }

}