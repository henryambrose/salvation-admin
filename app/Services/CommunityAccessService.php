<?php

namespace App\Services;

use App\Models\Member;
use App\Models\PPCHead;
use App\Models\SCCHead;
use App\Models\User;

class CommunityAccessService
{
    /**
     * Return list of allowed community IDs for the given user based on head roles.
     *
     * Returns null to indicate no restriction (e.g., superadmin or non-head roles),
     * an empty array [] to indicate no communities allowed.
     */
    public function getAllowedCommunityIds(User $user): ?array
    {
        if ($user->hasRole('superadmin')) {
            return null; // unrestricted
        }

        $hasHeadRole = $user->hasAnyRole(['ppc_head', 'scc_head']);
        if (! $hasHeadRole) {
            return null; // unrestricted for non-head roles; permissions still apply
        }

        // Map user to a Member record via email
        $member = Member::where('email', $user->email)->first();
        if (! $member) {
            return []; // head role but no linked member -> no access
        }

        $ppcCommunityIds = PPCHead::where('member_id', $member->id)->pluck('community_id')->all();
        $sccCommunityIds = SCCHead::where('member_id', $member->id)->pluck('community_id')->all();

        $merged = array_values(array_unique(array_filter(array_merge($ppcCommunityIds, $sccCommunityIds))));

        return $merged;
    }
}
