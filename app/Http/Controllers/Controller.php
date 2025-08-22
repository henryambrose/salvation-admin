<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Modules\Members\Models\Member;
use Modules\Members\Models\PPCHead;
use Modules\Members\Models\SCCHead;
use Modules\Members\Models\User;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // Base controller helper method
    protected function allowedCommunityIdsFor(User $user): ?array
    {
        if ($user->hasRole('superadmin')) {
            return null;
        } // Superadmin sees all
        if (! $user->hasAnyRole(['ppc-head', 'scc-head'])) {
            return null;
        } // Use hyphens, not underscores

        // Find member record by user email
        $member = Member::where('email', $user->email)->first();
        if (! $member) {
            return [];
        }

        // Get all communities this user manages
        $ppc = PPCHead::where('member_id', $member->id)->pluck('community_id')->all();
        $scc = SCCHead::where('member_id', $member->id)->pluck('community_id')->all();

        return array_values(array_unique(array_filter(array_merge($ppc, $scc))));
    }
}
