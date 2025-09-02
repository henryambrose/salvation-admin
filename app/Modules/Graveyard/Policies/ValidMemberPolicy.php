<?php

namespace Modules\Graveyard\Policies;

use App\Models\User;
use Modules\Graveyard\Models\ValidMember;
use Illuminate\Auth\Access\HandlesAuthorization;

class ValidMemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-valid-member');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ValidMember $validMember): bool
    {
        return $user->can('read-valid-member');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-valid-member');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ValidMember $validMember): bool
    {
        return $user->can('update-valid-member');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ValidMember $validMember): bool
    {
        return $user->can('delete-valid-member');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ValidMember $validMember): bool
    {
        return $user->can('restore-valid-member');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ValidMember $validMember): bool
    {
        return $user->can('delete-valid-member');
    }
}
