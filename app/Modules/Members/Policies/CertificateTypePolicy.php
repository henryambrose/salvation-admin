<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\User;
use Modules\Members\Models\CertificateType;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertificateTypePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any certificate types.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-certificate-type');
    }

    /**
     * Determine whether the user can view the certificate type.
     */
    public function view(User $user, CertificateType $certificateType): bool
    {
        return $user->can('read-certificate-type');
    }

    /**
     * Determine whether the user can create certificate types.
     */
    public function create(User $user): bool
    {
        return $user->can('create-certificate-type');
    }

    /**
     * Determine whether the user can update the certificate type.
     */
    public function update(User $user, CertificateType $certificateType): bool
    {
        return $user->can('update-certificate-type');
    }

    /**
     * Determine whether the user can delete the certificate type.
     */
    public function delete(User $user, CertificateType $certificateType): bool
    {
        return $user->can('delete-certificate-type') && $certificateType->canBeDeleted();
    }

    /**
     * Determine whether the user can restore the certificate type.
     */
    public function restore(User $user, CertificateType $certificateType): bool
    {
        return $user->can('restore-certificate-type');
    }

    /**
     * Determine whether the user can permanently delete the certificate type.
     */
    public function forceDelete(User $user, CertificateType $certificateType): bool
    {
        return $user->can('delete-certificate-type') && $user->hasRole('super admin');
    }
}