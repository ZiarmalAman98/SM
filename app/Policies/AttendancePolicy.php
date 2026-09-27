<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttendancePolicy
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

        if ($user->hasRole('teacher')) {
            return $user->can('ViewAny:Attendance');
        }

        return $user->hasRole('student')
            ? $user->can('View:OwnAttendance')
            : $user->hasRole('parent') && $user->can('View:ChildrenAttendance');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $attendance->teacher_id === $user->id
                && $user->can('View:Attendance');
        }

        if ($user->hasRole('student')) {
            return $attendance->student_id === $user->id
                && $user->can('View:OwnAttendance');
        }

        return $user->hasRole('parent')
            && $user->can('View:ChildrenAttendance')
            && $user->children1()->where('student_id', $attendance->student_id)->exists();
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user)
            || ($user->hasRole('teacher') && $user->can('Create:Attendance'));
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $this->isAdmin($user)
            || ($user->hasRole('teacher')
                && $attendance->teacher_id === $user->id
                && $user->can('Update:Attendance'));
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $this->isAdmin($user)
            && $user->can('Delete:Attendance');
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('DeleteAny:Attendance');
    }

    public function forceDelete(User $user, Attendance $attendance): bool
    {
        return $this->isAdmin($user)
            && $user->can('ForceDelete:Attendance');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('ForceDeleteAny:Attendance');
    }

    public function restore(User $user, Attendance $attendance): bool
    {
        return $this->isAdmin($user)
            && $user->can('Restore:Attendance');
    }

    public function restoreAny(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('RestoreAny:Attendance');
    }

    public function replicate(User $user, Attendance $attendance): bool
    {
        return $this->isAdmin($user)
            && $user->can('Replicate:Attendance');
    }

    public function reorder(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('Reorder:Attendance');
    }
}
