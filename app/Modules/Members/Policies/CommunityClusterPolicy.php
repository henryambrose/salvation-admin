<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\CommunityCluster;
use Modules\Members\Models\User;

class CommunityClusterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-community');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CommunityCluster $communityCluster): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('read-community');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('create-community');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CommunityCluster $communityCluster): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('update-community');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CommunityCluster $communityCluster): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-community');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CommunityCluster $communityCluster): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('restore-community');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CommunityCluster $communityCluster): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-community');
    }
}
