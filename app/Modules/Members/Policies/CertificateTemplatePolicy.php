<?php

namespace Modules\Members\Policies;

use Modules\Members\Models\User;
use Modules\Members\Models\CertificateTemplate;
use Illuminate\Auth\Access\HandlesAuthorization;

class CertificateTemplatePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any certificate templates.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('list-certificate-template') || $user->can('manage-certificate-templates');
    }

    /**
     * Determine whether the user can view the certificate template.
     */
    public function view(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return $user->can('read-certificate-template') || $user->can('manage-certificate-templates');
    }

    /**
     * Determine whether the user can create certificate templates.
     */
    public function create(User $user): bool
    {
        return $user->can('create-certificate-template') || $user->can('manage-certificate-templates');
    }

    /**
     * Determine whether the user can update the certificate template.
     */
    public function update(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return $user->can('update-certificate-template') || $user->can('manage-certificate-templates');
    }

    /**
     * Determine whether the user can delete the certificate template.
     */
    public function delete(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return ($user->can('delete-certificate-template') || $user->can('manage-certificate-templates'))
               && $certificateTemplate->canBeDeleted();
    }

    /**
     * Determine whether the user can restore the certificate template.
     */
    public function restore(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return $user->can('restore-certificate-template') || $user->can('manage-certificate-templates');
    }

    /**
     * Determine whether the user can permanently delete the certificate template.
     */
    public function forceDelete(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return ($user->can('delete-certificate-template') || $user->can('manage-certificate-templates'))
               && $user->hasRole('super admin');
    }

    /**
     * Determine whether the user can set template as default.
     */
    public function setDefault(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return $user->can('set-template-default') || $user->can('manage-certificate-templates');
    }

    /**
     * Determine whether the user can preview the template.
     */
    public function preview(User $user, CertificateTemplate $certificateTemplate): bool
    {
        return $user->can('preview-certificate-template') || $user->can('manage-certificate-templates');
    }
}