<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'teacher', 'student'])
            || $user->can('ViewAny:Assignment');
    }

    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return (int) $assignment->teacher_id === (int) $user->id;
        }

        if ($user->hasRole('student')) {
            return $assignment->subject()
                ->whereHas('schoolClass.studentClasses', fn ($q) =>
                    $q->where('student_id', $user->id)->where('status', 'active')
                )
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin'])
            || ($user->hasRole('teacher') && $user->can('Create:Assignment'));
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin'])
            || ($user->hasRole('teacher')
                && (int) $assignment->teacher_id === (int) $user->id
                && $user->can('Update:Assignment'));
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin'])
            || ($user->hasRole('teacher')
                && (int) $assignment->teacher_id === (int) $user->id
                && $user->can('Delete:Assignment'));
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) && $user->can('DeleteAny:Assignment');
    }
}
