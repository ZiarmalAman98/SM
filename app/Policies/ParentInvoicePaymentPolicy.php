<?php

namespace App\Policies;

use App\Models\ParentInvoicePayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParentInvoicePaymentPolicy
{
    use HandlesAuthorization;

    private function isAdmin(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    private function belongsToStudent(User $user, ParentInvoicePayment $payment): bool
    {
        $studentIds = $payment->invoice?->parentGuardian?->linkedStudents()?->pluck('student_id') ?? collect();

        return $studentIds->contains($user->id);
    }

    public function viewAny(User $user): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        return ($user->hasRole('student') && $user->can('View:OwnPayments'))
            || ($user->hasRole('parent') && $user->can('View:ChildrenPayments'));
    }

    public function view(User $user, ParentInvoicePayment $parentInvoicePayment): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $user->can('View:OwnPayments')
                && $this->belongsToStudent($user, $parentInvoicePayment);
        }

        if ($user->hasRole('parent')) {
            return $user->can('View:ChildrenPayments')
                && $parentInvoicePayment->invoice?->parentGuardian?->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user) && $user->can('Create:ParentInvoicePayment');
    }

    public function update(User $user, ParentInvoicePayment $parentInvoicePayment): bool
    {
        return $this->isAdmin($user) && $user->can('Update:ParentInvoicePayment');
    }

    public function delete(User $user, ParentInvoicePayment $parentInvoicePayment): bool
    {
        return $this->isAdmin($user) && $user->can('Delete:ParentInvoicePayment');
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user) && $user->can('DeleteAny:ParentInvoicePayment');
    }

    public function forceDelete(User $user, ParentInvoicePayment $parentInvoicePayment): bool
    {
        return $this->isAdmin($user) && $user->can('ForceDelete:ParentInvoicePayment');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->isAdmin($user) && $user->can('ForceDeleteAny:ParentInvoicePayment');
    }

    public function restore(User $user, ParentInvoicePayment $parentInvoicePayment): bool
    {
        return $this->isAdmin($user) && $user->can('Restore:ParentInvoicePayment');
    }

    public function restoreAny(User $user): bool
    {
        return $this->isAdmin($user) && $user->can('RestoreAny:ParentInvoicePayment');
    }

    public function replicate(User $user, ParentInvoicePayment $parentInvoicePayment): bool
    {
        return $this->isAdmin($user) && $user->can('Replicate:ParentInvoicePayment');
    }

    public function reorder(User $user): bool
    {
        return $this->isAdmin($user) && $user->can('Reorder:ParentInvoicePayment');
    }
}
