<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\ExternalMember;
use Modules\Members\Models\User;
use Modules\Members\Models\Member;

class ExternalMemberPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Superadmin has access to everything
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('list-external-member');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ExternalMember $externalMember): bool
    {
        // Superadmin has access to everything
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if (! $user->can('read-external-member')) {
            return false;
        }
        // Community-scoped access for head roles; otherwise allow
        $service = new \Modules\Members\Services\CommunityAccessService;
        $allowed = $service->getAllowedCommunityIds($user);
        if ($allowed === null) {
            return true;
        }
        // Prefer direct column
        if (! is_null($externalMember->community_id)) {
            return in_array($externalMember->community_id, $allowed, true);
        }
        // Fallback: check via family_no -> members community
        $memberCommunityId = Member::where('family_no', $externalMember->family_no)->value('community_id');

        return $memberCommunityId ? in_array($memberCommunityId, $allowed, true) : false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Superadmin has access to everything
        if ($user->hasRole('superadmin')) {
            return true;
        }

        return $user->can('create-external-member');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ExternalMember $externalMember): bool
    {
        // Superadmin has access to everything
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if (! $user->can('update-external-member')) {
            return false;
        }
        $service = new \Modules\Members\Services\CommunityAccessService;
        $allowed = $service->getAllowedCommunityIds($user);
        if ($allowed === null) {
            return true;
        }
        if (! is_null($externalMember->community_id)) {
            return in_array($externalMember->community_id, $allowed, true);
        }
        $memberCommunityId = Member::where('family_no', $externalMember->family_no)->value('community_id');

        return $memberCommunityId ? in_array($memberCommunityId, $allowed, true) : false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExternalMember $externalMember): bool
    {
        // Superadmin has access to everything
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if (! $user->can('delete-external-member')) {
            return false;
        }
        $service = new \Modules\Members\Services\CommunityAccessService;
        $allowed = $service->getAllowedCommunityIds($user);
        if ($allowed === null) {
            return true;
        }
        if (! is_null($externalMember->community_id)) {
            return in_array($externalMember->community_id, $allowed, true);
        }
        $memberCommunityId = Member::where('family_no', $externalMember->family_no)->value('community_id');

        return $memberCommunityId ? in_array($memberCommunityId, $allowed, true) : false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ExternalMember $externalMember): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if (! $user->can('restore-external-member')) {
            return false;
        }
        $service = new \Modules\Members\Services\CommunityAccessService;
        $allowed = $service->getAllowedCommunityIds($user);
        if ($allowed === null) {
            return true;
        }
        if (! is_null($externalMember->community_id)) {
            return in_array($externalMember->community_id, $allowed, true);
        }
        $memberCommunityId = Member::where('family_no', $externalMember->family_no)->value('community_id');

        return $memberCommunityId ? in_array($memberCommunityId, $allowed, true) : false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ExternalMember $externalMember): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // if (! $user->can('force-delete-external-member')) {
        //     return false;
        // }
        $service = new \Modules\Members\Services\CommunityAccessService;
        $allowed = $service->getAllowedCommunityIds($user);
        if ($allowed === null) {
            return true;
        }
        if (! is_null($externalMember->community_id)) {
            return in_array($externalMember->community_id, $allowed, true);
        }
        $memberCommunityId = Member::where('family_no', $externalMember->family_no)->value('community_id');

        return $memberCommunityId ? in_array($memberCommunityId, $allowed, true) : false;
    }
}
