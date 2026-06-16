<?php

namespace App\Filament\Student\Widgets;

use App\Models\Assignment;
use App\Models\ParentInvoicePayment;
use App\Models\StudentClass;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Get the authenticated user
        $user = Auth::user();

        // Total Enrollments
        $totalEnrollments = StudentClass::where('student_id', $user->id)->count();

        // Completed Courses
        $completedCourses = StudentClass::where('student_id', $user->id)
            ->where('status', 'completed')
            ->count();

        // Pending Assignments
        $pendingAssignments = Assignment::whereDoesntHave('submissions', function ($query) use ($user) {
            $query->where('student_id', $user->id);
        })->count();

        // Student Visits
        $studentVisits = \Illuminate\Support\Facades\DB::table('visitor_logs')
            ->where('person_to_meet', $user->id)
            ->count();

        // Total invoice payments for the student's family
        $totalPayments = ParentInvoicePayment::query()
            ->whereHas('invoice.parentGuardian.linkedStudents', fn($query) => $query->where('student_id', $user->id))
            ->sum('amount');

        return [
            Stat::make(__('Total Enrollments'), $totalEnrollments)
                ->description(__('Number of courses you are enrolled in'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make(__('Completed Courses'), $completedCourses)
                ->description(__('Courses you have completed'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('Pending Assignments'), $pendingAssignments)
                ->description(__('Assignments yet to be submitted'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('Student Visits'), $studentVisits)
                ->description(__('Number of visits to the platform'))
                ->descriptionIcon('heroicon-m-clipboard')
                ->color('primary'),

            Stat::make(__('Total Invoice Payments'), __('AF :amount', ['amount' => number_format($totalPayments, 2)]))
                ->description(__('Total amount paid for invoices'))
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('success'),
        ];
    }
}
