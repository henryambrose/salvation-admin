<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\Member;
use Modules\Members\Models\User;

class MemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        \Log::info('MemberPolicy viewAny called for user: ' . $user->id);
        \Log::info('User permissions: ' . $user->getAllPermissions()->pluck('name'));
        
        if ($user->hasRole('superadmin')) {
            \Log::info('User has superadmin role');
            return true;
        }

        $canList = $user->can('list-member');
        \Log::info('User can list-member: ' . ($canList ? 'true' : 'false'));
        
        return $canList;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Member $member): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }
        if (! $user->can('read-member')) {
            return false;
        }
        // Optional: enforce community scope at policy level
        $service = new \Modules\Members\Services\CommunityAccessService;
        $allowed = $service->getAllowedCommunityIds($user);
        if ($allowed === null) {
            return true;
        }

        return in_array($member->community_id, $allowed ?? [], true);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('create-member');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Member $member): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('update-member');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Member $member): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-member');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Member $member): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('restore-member');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Member $member): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('delete-member');
    }
}
