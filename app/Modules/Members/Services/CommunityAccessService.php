<?php

namespace Modules\Members\Services;

use Modules\Members\Models\Member;
use Modules\Members\Models\PPCHead;
use Modules\Members\Models\SCCHead;
use Modules\Members\Models\User;

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

        $hasHeadRole = $user->hasAnyRole(['ppc-head', 'scc-head']);
        if (! $hasHeadRole) {
            return null; // unrestricted for non-head roles
        }

        // Initialize variables
        $ppcCommunityIds = [];
        $sccCommunityIds = [];

        // First try to find member record by email
        $member = Member::where('email', $user->email)->first();
        
        if ($member) {
            // Found member record, get communities from PPC/SCC head tables
            $ppcCommunityIds = PPCHead::where('member_id', $member->id)->pluck('community_id')->all();
            $sccCommunityIds = SCCHead::where('member_id', $member->id)->pluck('community_id')->all();
        } else {
            // No member record found, try to find communities directly
            // Look for PPCHead records where the member has this email
            $ppcCommunityIds = PPCHead::whereHas('member', function($query) use ($user) {
                $query->where('email', $user->email);
            })->pluck('community_id')->all();
            
            // Look for SCCHead records where the member has this email
            $sccCommunityIds = SCCHead::whereHas('member', function($query) use ($user) {
                $query->where('email', $user->email);
            })->pluck('community_id')->all();
        }

        $merged = array_values(array_unique(array_filter(array_merge($ppcCommunityIds, $sccCommunityIds))));

        return $merged;
    }
}
