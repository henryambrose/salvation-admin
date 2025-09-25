<?php

namespace App\Policies;

use Modules\Graveyard\Models\ObituaryPlan;
use Illuminate\Auth\Access\Response;
use Modules\Members\Models\User;

class ObituaryPlanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow any user with graveyard access to view obituary plans
        return $user->can('access-graveyard');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ObituaryPlan $obituaryPlan): bool
    {
        return $user->can('access-graveyard') &&
               ($user->hasRole('Super Admin') || $user->can('obituary-plans.view'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('access-graveyard') &&
               ($user->hasRole('Super Admin') || $user->can('obituary-plans.create'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ObituaryPlan $obituaryPlan): bool
    {
        return $user->can('access-graveyard') &&
               ($user->hasRole('Super Admin') || $user->can('obituary-plans.update'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ObituaryPlan $obituaryPlan): bool
    {
        return $user->can('access-graveyard') &&
               ($user->hasRole('Super Admin') || $user->can('obituary-plans.delete'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ObituaryPlan $obituaryPlan): bool
    {
        return $user->can('access-graveyard') &&
               ($user->hasRole('Super Admin') || $user->can('obituary-plans.restore'));
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ObituaryPlan $obituaryPlan): bool
    {
        return $user->can('access-graveyard') &&
               ($user->hasRole('Super Admin') || $user->can('obituary-plans.forceDelete'));
    }
}
