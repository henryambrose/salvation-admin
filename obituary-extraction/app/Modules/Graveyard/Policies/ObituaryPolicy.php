<?php

namespace Modules\Graveyard\Policies;

use Modules\Members\Models\User;
use Modules\Graveyard\Models\ObituaryPage;
use Illuminate\Auth\Access\Response;

class ObituaryPolicy
{
    /**
     * Determine whether the user can view any obituaries.
     */
    public function viewAny(User $user): bool
    {
        // Superadmin can view all obituaries
        if ($user->is_superadmin) {
            return true;
        }

        // Check if user has permission to view obituaries
        return $user->can('view-obituaries') ||
            $user->hasRole(['admin', 'superadmin', 'super admin']);
    }

    /**
     * Determine whether the user can view the obituary.
     */
    public function view(User $user, ObituaryPage $obituary): bool
    {
        // Superadmin can view all obituaries
        if ($user->is_superadmin) {
            return true;
        }

        // Admin users can view all obituaries
        if (
            $user->hasRole(['admin', 'superadmin', 'super admin']) ||
            $user->can('view-all-obituaries')
        ) {
            return true;
        }

        // Check if user is the creator
        if ($obituary->created_by === $user->id) {
            return true;
        }

        // Check if user is related to the booking
        if ($obituary->permanent_grave_booking) {
            // Check if user created the original booking
            if ($obituary->permanent_grave_booking->created_by === $user->id) {
                return true;
            }

            // Check if user is the applicant member
            if (
                $obituary->permanent_grave_booking->applicant_type === 'member' &&
                $obituary->permanent_grave_booking->applicant_member_id === $user->member_id
            ) {
                return true;
            }
        }

        if ($obituary->temporary_grave_booking) {
            // Check if user created the original booking
            if ($obituary->temporary_grave_booking->created_by === $user->id) {
                return true;
            }

            // Check if user is the applicant member
            if (
                $obituary->temporary_grave_booking->applicant_type === 'member' &&
                $obituary->temporary_grave_booking->applicant_member_id === $user->member_id
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create obituaries.
     */
    public function create(User $user): bool
    {
        // Superadmin can create obituaries
        if ($user->is_superadmin) {
            return true;
        }

        // Check if user has permission to create obituaries
        return $user->can('create-obituaries') ||
            $user->hasRole(['admin', 'superadmin', 'super admin']);
    }

    /**
     * Determine whether the user can update the obituary.
     */
    public function update(User $user, ObituaryPage $obituary): bool
    {
        // Superadmin can update all obituaries
        if ($user->is_superadmin) {
            return true;
        }

        // Admin users can update all obituaries
        if (
            $user->hasRole(['admin', 'superadmin', 'super admin']) ||
            $user->can('update-all-obituaries')
        ) {
            return true;
        }

        // Check if user is the creator
        if ($obituary->created_by === $user->id) {
            return true;
        }

        // Check if user is related to the booking and has update permission
        if ($user->can('update-own-obituaries')) {
            if ($obituary->permanent_grave_booking) {
                // Check if user created the original booking
                if ($obituary->permanent_grave_booking->created_by === $user->id) {
                    return true;
                }

                // Check if user is the applicant member
                if (
                    $obituary->permanent_grave_booking->applicant_type === 'member' &&
                    $obituary->permanent_grave_booking->applicant_member_id === $user->member_id
                ) {
                    return true;
                }
            }

            if ($obituary->temporary_grave_booking) {
                // Check if user created the original booking
                if ($obituary->temporary_grave_booking->created_by === $user->id) {
                    return true;
                }

                // Check if user is the applicant member
                if (
                    $obituary->temporary_grave_booking->applicant_type === 'member' &&
                    $obituary->temporary_grave_booking->applicant_member_id === $user->member_id
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the obituary.
     */
    public function delete(User $user, ObituaryPage $obituary): bool
    {
        // Superadmin can delete all obituaries
        if ($user->is_superadmin) {
            return true;
        }

        // Admin users can delete all obituaries
        if (
            $user->hasRole(['admin', 'superadmin', 'super admin']) ||
            $user->can('delete-all-obituaries')
        ) {
            return true;
        }

        // Check if user is the creator and has delete permission
        if ($obituary->created_by === $user->id && $user->can('delete-own-obituaries')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can manage condolences.
     */
    public function manageCondolences(User $user): bool
    {
        // Superadmin can manage all condolences
        if ($user->is_superadmin) {
            return true;
        }

        // Check if user has permission to manage condolences
        return $user->can('manage-condolences') ||
            $user->hasRole(['admin', 'superadmin', 'super admin']);
    }

    /**
     * Determine whether the user can approve condolences.
     */
    public function approveCondolences(User $user): bool
    {
        // Superadmin can approve all condolences
        if ($user->is_superadmin) {
            return true;
        }

        // Check if user has permission to approve condolences
        return $user->can('approve-condolences') ||
            $user->hasRole(['admin', 'superadmin', 'super admin']);
    }

    /**
     * Determine whether the user can process payments for obituaries.
     */
    public function processPayments(User $user): bool
    {
        // Superadmin can process all payments
        if ($user->is_superadmin) {
            return true;
        }

        // Check if user has permission to process payments
        return $user->can('process-obituary-payments') ||
            $user->hasRole(['admin', 'superadmin', 'super admin']);
    }

    /**
     * Determine whether the user can generate custom QR codes.
     */
    public function generateQrCode(User $user, ObituaryPage $obituary): bool
    {
        // Use the same logic as update - if you can update, you can generate QR codes
        return $this->update($user, $obituary);
    }

    /**
     * Determine whether the user can preview obituaries (even inactive ones).
     */
    public function preview(User $user, ObituaryPage $obituary): bool
    {
        // Superadmin can preview all obituaries
        if ($user->is_superadmin) {
            return true;
        }

        // Admin users can preview all obituaries
        if ($user->hasRole(['admin', 'superadmin', 'super admin'])) {
            return true;
        }

        // If user can view any obituaries (has access to management interface)
        if ($this->viewAny($user)) {
            return true;
        }

        // Fallback to specific obituary view permission
        return $this->view($user, $obituary);
    }

    /**
     * Determine whether the user can publish obituary pages.
     */
    public function publish(User $user, ObituaryPage $obituary): bool
    {
        // Superadmin can publish all obituaries
        if ($user->is_superadmin) {
            return true;
        }

        // Admin users can publish all obituaries
        if ($user->hasRole(['admin', 'superadmin', 'super admin'])) {
            return true;
        }

        // Check if user has specific publish permission
        if ($user->can('publish-obituary-page')) {
            // Can publish if they can also update the obituary
            return $this->update($user, $obituary);
        }

        return false;
    }

    /**
     * Determine whether the user can unpublish obituary pages.
     */
    public function unpublish(User $user, ObituaryPage $obituary): bool
    {
        // Superadmin can unpublish all obituaries
        if ($user->is_superadmin) {
            return true;
        }

        // Admin users can unpublish all obituaries
        if ($user->hasRole(['admin', 'superadmin', 'super admin'])) {
            return true;
        }

        // Check if user has specific unpublish permission
        if ($user->can('unpublish-obituary-page')) {
            // Can unpublish if they can also update the obituary
            return $this->update($user, $obituary);
        }

        return false;
    }

    /**
     * Determine whether the user can reject condolences.
     */
    public function rejectCondolences(User $user): bool
    {
        // Superadmin can reject all condolences
        if ($user->is_superadmin) {
            return true;
        }

        // Check if user has permission to reject condolences
        return $user->can('reject-obituary-condolence') ||
            $user->hasRole(['admin', 'superadmin', 'super admin']);
    }
}
