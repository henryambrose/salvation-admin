<?php

namespace Modules\Members\Policies;

use App\Models\User;
use Modules\Members\Models\BirthArchiveCertificate;
use Illuminate\Auth\Access\HandlesAuthorization;

class BirthArchiveCertificatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any birth archive certificates.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-birth-archive');
    }

    /**
     * Determine whether the user can view the birth archive certificate.
     */
    public function view(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('read-birth-archive');
    }

    /**
     * Determine whether the user can create birth archive certificates.
     */
    public function create(User $user): bool
    {
        return $user->can('create-birth-archive');
    }

    /**
     * Determine whether the user can update the birth archive certificate.
     */
    public function update(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('update-birth-archive');
    }

    /**
     * Determine whether the user can delete the birth archive certificate.
     */
    public function delete(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('delete-birth-archive');
    }

    /**
     * Determine whether the user can restore the birth archive certificate.
     */
    public function restore(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('restore-birth-archive');
    }

    /**
     * Determine whether the user can download the birth archive certificate.
     */
    public function download(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('download-birth-archive');
    }

    /**
     * Determine whether the user can permanently delete the birth archive certificate.
     */
    public function forceDelete(User $user, BirthArchiveCertificate $certificate): bool
    {
        return $user->can('delete-birth-archive') && $user->hasRole('super admin');
    }
}
