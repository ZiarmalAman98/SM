<?php

namespace App\Policies;

use App\Models\FeePayment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeePaymentPolicy
{
    use HandlesAuthorization;

    private function isAdmin(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    public function viewAny(User $user): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $user->can('View:OwnFees');
        }

        return $user->hasRole('parent') && $user->can('View:ChildrenFees');
    }

    public function view(User $user, FeePayment $feePayment): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $feePayment->student_id === $user->id
                && $user->can('View:OwnFees');
        }

        return $user->hasRole('parent')
            && $user->can('View:ChildrenFees')
            && $user->children1()->where('student_id', $feePayment->student_id)->exists();
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('Create:FeePayment');
    }

    public function update(User $user, FeePayment $feePayment): bool
    {
        return $this->isAdmin($user)
            && $user->can('Update:FeePayment');
    }

    public function delete(User $user, FeePayment $feePayment): bool
    {
        return $this->isAdmin($user)
            && $user->can('Delete:FeePayment');
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('DeleteAny:FeePayment');
    }

    public function forceDelete(User $user, FeePayment $feePayment): bool
    {
        return $this->isAdmin($user)
            && $user->can('ForceDelete:FeePayment');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('ForceDeleteAny:FeePayment');
    }

    public function restore(User $user, FeePayment $feePayment): bool
    {
        return $this->isAdmin($user)
            && $user->can('Restore:FeePayment');
    }

    public function restoreAny(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('RestoreAny:FeePayment');
    }

    public function replicate(User $user, FeePayment $feePayment): bool
    {
        return $this->isAdmin($user)
            && $user->can('Replicate:FeePayment');
    }

    public function reorder(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('Reorder:FeePayment');
    }
}
