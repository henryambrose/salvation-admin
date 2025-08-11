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

        $hasHeadRole = $user->hasAnyRole(['ppc-head', 'scc-head']);
        if (! $hasHeadRole) {
            return null; // unrestricted for non-head roles
        }

        \Log::info('CommunityAccessService debug', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_roles' => $user->getRoleNames()->toArray(),
        ]);

        // Initialize variables
        $ppcCommunityIds = [];
        $sccCommunityIds = [];

        // First try to find member record by email
        $member = Member::where('email', $user->email)->first();
        
        if ($member) {
            // Found member record, get communities from PPC/SCC head tables
            $ppcCommunityIds = PPCHead::where('member_id', $member->id)->pluck('community_id')->all();
            $sccCommunityIds = SCCHead::where('member_id', $member->id)->pluck('community_id')->all();
            
            \Log::info('CommunityAccessService - member record found', [
                'member_id' => $member->id,
                'ppc_community_ids' => $ppcCommunityIds,
                'scc_community_ids' => $sccCommunityIds,
            ]);
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
            
            \Log::info('CommunityAccessService - no member record, checking head tables directly', [
                'ppc_community_ids' => $ppcCommunityIds,
                'scc_community_ids' => $sccCommunityIds,
            ]);
        }

        $merged = array_values(array_unique(array_filter(array_merge($ppcCommunityIds, $sccCommunityIds))));
        
        \Log::info('CommunityAccessService - final result', [
            'merged_community_ids' => $merged,
        ]);

        return $merged;
    }
}
