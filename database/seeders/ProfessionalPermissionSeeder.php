<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProfessionalPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $guard = 'web';

        /*
         * Keep the application authorization role-based.
         * "parent" maps to the existing users.type = guardian.
         * "user" maps to general/staff users.
         */
        $roles = [
            'super_admin',
            'admin',
            'user',
            'teacher',
            'student',
            'parent',
        ];

        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName, $guard);
        }

        /*
         * Filament Shield permissions are generated from actual Resources,
         * Pages and Widgets. Run shield:generate before assigning them.
         */
        $this->ensureCustomPermissions($guard);

        $admin = Role::findByName('admin', $guard);
        $admin->syncPermissions(Permission::where('guard_name', $guard)->pluck('name')->all());

        $teacher = Role::findByName('teacher', $guard);
        $teacher->syncPermissions($this->permissionsFor('teacher', $guard));

        $student = Role::findByName('student', $guard);
        $student->syncPermissions($this->permissionsFor('student', $guard));

        $parent = Role::findByName('parent', $guard);
        $parent->syncPermissions($this->permissionsFor('parent', $guard));

        $user = Role::findByName('user', $guard);
        $user->syncPermissions($this->permissionsFor('user', $guard));

        /*
         * Super admin is handled through Gate::before, so it does not need
         * thousands of duplicated permission rows.
         */
        Role::findByName('super_admin', $guard)->syncPermissions([]);

        $this->assignRolesToUsers($guard);
    }

    private function ensureCustomPermissions(string $guard): void
    {
        $permissions = [
            'View:Dashboard',
            'View:OwnProfile',
            'Update:OwnProfile',

            'View:OwnAttendance',
            'View:OwnHomework',
            'View:OwnExamResults',
            'View:OwnFees',
            'View:OwnPayments',
            'View:OwnLeave',

            'View:Children',
            'View:ChildrenAttendance',
            'View:ChildrenHomework',
            'View:ChildrenExamResults',
            'View:ChildrenFees',
            'View:ChildrenPayments',
            'Create:LeaveRequest',
            'View:Notifications',
            'View:Chat',

            'Approve:Attendance',
            'Approve:Leave',
            'Approve:Expense',
            'Approve:Payment',
            'Export:Reports',
            'Manage:Users',
            'Manage:Settings',
            'View:FinancialReports',

            // Sensitive academic and financial resource permissions.
            'ViewAny:Attendance',
            'View:Attendance',
            'Create:Attendance',
            'Update:Attendance',
            'Delete:Attendance',
            'DeleteAny:Attendance',
            'ForceDelete:Attendance',
            'ForceDeleteAny:Attendance',
            'Restore:Attendance',
            'RestoreAny:Attendance',
            'Replicate:Attendance',
            'Reorder:Attendance',

            'ViewAny:ExamResult',
            'View:ExamResult',
            'Create:ExamResult',
            'Update:ExamResult',
            'Delete:ExamResult',
            'DeleteAny:ExamResult',
            'ForceDelete:ExamResult',
            'ForceDeleteAny:ExamResult',
            'Restore:ExamResult',
            'RestoreAny:ExamResult',
            'Replicate:ExamResult',
            'Reorder:ExamResult',

            'ViewAny:FeePayment',
            'View:FeePayment',
            'Create:FeePayment',
            'Update:FeePayment',
            'Delete:FeePayment',
            'DeleteAny:FeePayment',
            'ForceDelete:FeePayment',
            'ForceDeleteAny:FeePayment',
            'Restore:FeePayment',
            'RestoreAny:FeePayment',
            'Replicate:FeePayment',
            'Reorder:FeePayment',

            'ViewAny:ParentInvoicePayment',
            'View:ParentInvoicePayment',
            'Create:ParentInvoicePayment',
            'Update:ParentInvoicePayment',
            'Delete:ParentInvoicePayment',
            'DeleteAny:ParentInvoicePayment',
            'ForceDelete:ParentInvoicePayment',
            'ForceDeleteAny:ParentInvoicePayment',
            'Restore:ParentInvoicePayment',
            'RestoreAny:ParentInvoicePayment',
            'Replicate:ParentInvoicePayment',
            'Reorder:ParentInvoicePayment',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, $guard);
        }
    }

    private function permissionsFor(string $role, string $guard): array
    {
        $all = Permission::where('guard_name', $guard)->pluck('name')->all();

        if ($role === 'user') {
            return $this->matching($all, [
                'View:Dashboard',
                'View:OwnProfile',
                'Update:OwnProfile',
                'View:Notifications',
                'View:Chat',
                'View:',
                'ViewAny:',
            ], except: [
                'View:Financial',
                'View:Payroll',
            ]);
        }

        if ($role === 'teacher') {
            return $this->matching($all, [
                'View:Dashboard',
                'View:OwnProfile',
                'Update:OwnProfile',
                'View:Student',
                'ViewAny:Student',
                'View:SchoolClass',
                'ViewAny:SchoolClass',
                'View:Section',
                'ViewAny:Section',
                'View:Subject',
                'ViewAny:Subject',
                'View:Attendance',
                'ViewAny:Attendance',
                'Create:Attendance',
                'Update:Attendance',
                'View:Homework',
                'ViewAny:Homework',
                'Create:Homework',
                'Update:Homework',
                'View:Exam',
                'ViewAny:Exam',
                'Create:Exam',
                'Update:Exam',
                'View:ExamResult',
                'ViewAny:ExamResult',
                'View:Reports',
                'ViewAny:Reports',
                'View:Notifications',
                'View:Chat',
            ]);
        }

        if ($role === 'student') {
            return $this->matching($all, [
                'View:Dashboard',
                'View:OwnProfile',
                'Update:OwnProfile',
                'View:OwnAttendance',
                'View:OwnHomework',
                'View:OwnExamResults',
                'View:OwnFees',
                'View:OwnPayments',
                'View:OwnLeave',
                'Create:LeaveRequest',
                'View:Notifications',
                'View:Chat',
                'View:Student',
                'View:SchoolClass',
                'View:Section',
                'View:Subject',
                'View:Homework',
                'View:Exam',
                'View:ExamResult',
            ]);
        }

        if ($role === 'parent') {
            return $this->matching($all, [
                'View:Dashboard',
                'View:OwnProfile',
                'Update:OwnProfile',
                'View:Children',
                'View:ChildrenAttendance',
                'View:ChildrenHomework',
                'View:ChildrenExamResults',
                'View:ChildrenFees',
                'View:ChildrenPayments',
                'View:Notifications',
                'View:Chat',
                'View:Student',
                'View:SchoolClass',
                'View:Section',
                'View:Subject',
                'View:Homework',
                'View:Exam',
                'View:ExamResult',
            ]);
        }

        return [];
    }

    private function matching(array $all, array $allowed, array $except = []): array
    {
        return collect($all)
            ->filter(function (string $permission) use ($allowed, $except) {
                foreach ($except as $blocked) {
                    if (str_starts_with($permission, $blocked)) {
                        return false;
                    }
                }

                foreach ($allowed as $prefix) {
                    if (str_starts_with($permission, $prefix)) {
                        return true;
                    }
                }

                return false;
            })
            ->values()
            ->all();
    }

    private function assignRolesToUsers(string $guard): void
    {
        User::query()->each(function (User $user) use ($guard): void {
            $role = match ($user->type) {
                'admin' => 'admin',
                'teacher' => 'teacher',
                'student' => 'student',
                'guardian' => 'parent',
                'staff' => 'user',
                default => 'user',
            };

            $user->syncRoles([$role]);
        });
    }
}
