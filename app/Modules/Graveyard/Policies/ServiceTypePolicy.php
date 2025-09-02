<?php

namespace Modules\Graveyard\Policies;

use App\Models\User;
use Modules\Graveyard\Models\ServiceType;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-service-type');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceType $serviceType): bool
    {
        return $user->can('read-service-type');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-service-type');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceType $serviceType): bool
    {
        return $user->can('update-service-type');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceType $serviceType): bool
    {
        return $user->can('delete-service-type');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceType $serviceType): bool
    {
        return $user->can('restore-service-type');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceType $serviceType): bool
    {
        return $user->can('delete-service-type');
    }

    /**
     * Determine whether the user can toggle active status.
     */
    public function toggleActive(User $user, ServiceType $serviceType): bool
    {
        return $user->can('update-service-type');
    }
}