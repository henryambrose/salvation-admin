<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // Base controller helper method
    protected function allowedCommunityIdsFor(\App\Models\User $user): ?array
    {
        if ($user->hasRole('superadmin')) return null; // Superadmin sees all
        if (!$user->hasAnyRole(['ppc-head','scc-head'])) return null; // Use hyphens, not underscores

        // Find member record by user email
        $member = \App\Models\Member::where('email', $user->email)->first();
        if (!$member) return [];

        // Get all communities this user manages
        $ppc = \App\Models\PPCHead::where('member_id', $member->id)->pluck('community_id')->all();
        $scc = \App\Models\SCCHead::where('member_id', $member->id)->pluck('community_id')->all();
        
        return array_values(array_unique(array_filter(array_merge($ppc, $scc))));
    }
}
