<?php

namespace App\Policies;

use App\Models\AssignmentSubmission;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssignmentSubmissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'teacher', 'student'])
            || $user->can('ViewAny:AssignmentSubmission');
    }

    public function view(User $user, AssignmentSubmission $submission): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return (int) $submission->assignment?->teacher_id === (int) $user->id;
        }

        if ($user->hasRole('student')) {
            return (int) $submission->student_id === (int) $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin'])
            || ($user->hasRole('student') && $user->can('Create:AssignmentSubmission'));
    }

    public function update(User $user, AssignmentSubmission $submission): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        return $user->hasRole('student')
            && (int) $submission->student_id === (int) $user->id
            && $user->can('Update:AssignmentSubmission');
    }

    public function delete(User $user, AssignmentSubmission $submission): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        return $user->hasRole('student')
            && (int) $submission->student_id === (int) $user->id
            && $user->can('Delete:AssignmentSubmission');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin']) && $user->can('DeleteAny:AssignmentSubmission');
    }
}
