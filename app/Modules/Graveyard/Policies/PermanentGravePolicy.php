<?php

namespace Modules\Graveyard\Policies;

use Modules\Graveyard\Models\PermanentGrave;
use Illuminate\Auth\Access\Response;
use Modules\Members\Models\User;

class PermanentGravePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PermanentGrave $permanentGrave): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PermanentGrave $permanentGrave): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PermanentGrave $permanentGrave): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PermanentGrave $permanentGrave): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PermanentGrave $permanentGrave): bool
    {
        return false;
    }
}
