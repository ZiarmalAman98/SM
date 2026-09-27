<?php

namespace App\Policies;

use App\Models\ParentStudent;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParentStudentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'parent'])
            || $user->can('View:Children');
    }

    public function view(User $user, ParentStudent $parentStudent): bool
    {
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            return true;
        }

        return $user->hasRole('parent')
            && (int) $parentStudent->parent_guardian_id === (int) $user->id
            && $user->can('View:Children');
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin'])
            && $user->can('Create:ParentStudent');
    }

    public function update(User $user, ParentStudent $parentStudent): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin'])
            && $user->can('Update:ParentStudent');
    }

    public function delete(User $user, ParentStudent $parentStudent): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin'])
            && $user->can('Delete:ParentStudent');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin'])
            && $user->can('DeleteAny:ParentStudent');
    }
}
