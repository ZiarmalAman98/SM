<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin'])
            || $user->can('ViewAny:Student');
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            return (int) $student->user_id === (int) $user->id
                && ($user->can('View:OwnProfile') || $user->can('View:Student'));
        }

        if ($user->hasRole('parent')) {
            return $user->children1()
                ->where('student_id', $student->user_id)
                ->exists()
                && ($user->can('View:Children') || $user->can('View:Student'));
        }

        return $user->can('View:Student');
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin'])
            || $user->can('Create:Student');
    }

    public function update(User $user, Student $student): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            return (int) $student->user_id === (int) $user->id
                && $user->can('Update:OwnProfile');
        }

        return $user->can('Update:Student');
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin'])
            && $user->can('Delete:Student');
    }
}
