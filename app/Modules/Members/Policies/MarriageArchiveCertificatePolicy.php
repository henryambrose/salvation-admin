<?php

namespace Modules\Members\Policies;

use App\Models\User;
use Modules\Members\Models\MarriageArchiveCertificate;
use Illuminate\Auth\Access\HandlesAuthorization;

class MarriageArchiveCertificatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any marriage archive certificates.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-marriage-archive');
    }

    /**
     * Determine whether the user can view the marriage archive certificate.
     */
    public function view(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('read-marriage-archive');
    }

    /**
     * Determine whether the user can create marriage archive certificates.
     */
    public function create(User $user): bool
    {
        return $user->can('create-marriage-archive');
    }

    /**
     * Determine whether the user can update the marriage archive certificate.
     */
    public function update(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('update-marriage-archive');
    }

    /**
     * Determine whether the user can delete the marriage archive certificate.
     */
    public function delete(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('delete-marriage-archive');
    }

    /**
     * Determine whether the user can restore the marriage archive certificate.
     */
    public function restore(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('restore-marriage-archive');
    }

    /**
     * Determine whether the user can download the marriage archive certificate.
     */
    public function download(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('download-marriage-archive');
    }

    /**
     * Determine whether the user can permanently delete the marriage archive certificate.
     */
    public function forceDelete(User $user, MarriageArchiveCertificate $certificate): bool
    {
        return $user->can('delete-marriage-archive') && $user->hasRole('super admin');
    }
}
