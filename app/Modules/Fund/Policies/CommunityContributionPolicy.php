<?php

namespace Modules\Fund\Policies;

use Modules\Fund\Models\CommunityContribution;
use Modules\Members\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommunityContributionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any community contributions.
     */
    public function viewAny(User $user): bool
    {
        // Super admin can view all
        if ($user->is_superadmin) {
            return true;
        }

        // Users with list-community-contribution permission can view
        return $user->can('list-community-contribution');
    }

    /**
     * Determine if the user can view the community contribution.
     */
    public function view(User $user, CommunityContribution $communityContribution): bool
    {
        // Super admin can view all
        if ($user->is_superadmin) {
            return true;
        }

        // Users with read-community-contribution permission can view
        return $user->can('read-community-contribution');
    }

    /**
     * Determine if the user can create community contributions.
     */
    public function create(User $user): bool
    {
        // Super admin can create
        if ($user->is_superadmin) {
            return true;
        }

        // Users with create-community-contribution permission can create
        return $user->can('create-community-contribution');
    }

    /**
     * Determine if the user can update the community contribution.
     */
    public function update(User $user, CommunityContribution $communityContribution): bool
    {
        // Super admin can update all
        if ($user->is_superadmin) {
            return true;
        }

        // Users with update-community-contribution permission can update
        return $user->can('update-community-contribution');
    }

    /**
     * Determine if the user can delete the community contribution.
     */
    public function delete(User $user, CommunityContribution $communityContribution): bool
    {
        // Super admin can delete all
        if ($user->is_superadmin) {
            return true;
        }

        // Users with delete-community-contribution permission can delete
        return $user->can('delete-community-contribution');
    }

    /**
     * Determine if the user can restore the community contribution.
     */
    public function restore(User $user, CommunityContribution $communityContribution): bool
    {
        // Super admin can restore all
        if ($user->is_superadmin) {
            return true;
        }

        // Users with restore-community-contribution permission can restore
        return $user->can('restore-community-contribution');
    }

    /**
     * Determine if the user can permanently delete the community contribution.
     */
    public function forceDelete(User $user, CommunityContribution $communityContribution): bool
    {
        // Only super admin can force delete
        return $user->is_superadmin;
    }
}