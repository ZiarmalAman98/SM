<?php

namespace App\Policies;

use App\Models\ExamResult;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamResultPolicy
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
            return $user->can('ViewAny:ExamResult');
        }

        if ($user->hasRole('student')) {
            return $user->can('View:OwnExamResults');
        }

        return $user->hasRole('parent') && $user->can('View:ChildrenExamResults');
    }

    public function view(User $user, ExamResult $examResult): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $user->can('View:ExamResult')
                && $examResult->subject?->teacher_id === $user->id;
        }

        if ($user->hasRole('student')) {
            return $examResult->student_id === $user->id
                && $user->can('View:OwnExamResults');
        }

        return $user->hasRole('parent')
            && $user->can('View:ChildrenExamResults')
            && $user->children1()->where('student_id', $examResult->student_id)->exists();
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user)
            || ($user->hasRole('teacher') && $user->can('Create:ExamResult'));
    }

    public function update(User $user, ExamResult $examResult): bool
    {
        return $this->isAdmin($user)
            || ($user->hasRole('teacher')
                && $examResult->subject?->teacher_id === $user->id
                && $user->can('Update:ExamResult'));
    }

    public function delete(User $user, ExamResult $examResult): bool
    {
        return $this->isAdmin($user)
            && $user->can('Delete:ExamResult');
    }

    public function deleteAny(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('DeleteAny:ExamResult');
    }

    public function forceDelete(User $user, ExamResult $examResult): bool
    {
        return $this->isAdmin($user)
            && $user->can('ForceDelete:ExamResult');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('ForceDeleteAny:ExamResult');
    }

    public function restore(User $user, ExamResult $examResult): bool
    {
        return $this->isAdmin($user)
            && $user->can('Restore:ExamResult');
    }

    public function restoreAny(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('RestoreAny:ExamResult');
    }

    public function replicate(User $user, ExamResult $examResult): bool
    {
        return $this->isAdmin($user)
            && $user->can('Replicate:ExamResult');
    }

    public function reorder(User $user): bool
    {
        return $this->isAdmin($user)
            && $user->can('Reorder:ExamResult');
    }
}
