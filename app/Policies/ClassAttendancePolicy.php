<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ClassAttendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClassAttendancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_class::attendance::report');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ClassAttendance $classAttendance): bool
    {
        return $user->can('view_class::attendance::report');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_class::attendance::report');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ClassAttendance $classAttendance): bool
    {
        return $user->can('update_class::attendance::report');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ClassAttendance $classAttendance): bool
    {
        return $user->can('delete_class::attendance::report');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_class::attendance::report');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ClassAttendance $classAttendance): bool
    {
        return $user->can('force_delete_class::attendance::report');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_class::attendance::report');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ClassAttendance $classAttendance): bool
    {
        return $user->can('restore_class::attendance::report');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_class::attendance::report');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ClassAttendance $classAttendance): bool
    {
        return $user->can('replicate_class::attendance::report');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_class::attendance::report');
    }
}
