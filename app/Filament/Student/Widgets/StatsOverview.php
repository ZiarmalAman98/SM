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
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();

        $activeClasses = StudentClass::query()
            ->where('student_id', $user->id)
            ->where('status', 'active');

        $totalEnrollments = (clone $activeClasses)->count();

        $completedCourses = StudentClass::query()
            ->where('student_id', $user->id)
            ->where('status', 'completed')
            ->count();

        // Only count assignments belonging to the student's active class.
        $pendingAssignments = Assignment::query()
            ->whereHas('subject.schoolClass.studentClasses', function ($query) use ($user) {
                $query->where('student_id', $user->id)
                    ->where('status', 'active');
            })
            ->whereDoesntHave('submissions', function ($query) use ($user) {
                $query->where('student_id', $user->id);
            })
            ->where(function ($query) {
                $query->whereNull('deadline')
                    ->orWhere('deadline', '>=', now());
            })
            ->count();

        $studentVisits = \Illuminate\Support\Facades\DB::table('visitor_logs')
            ->where('person_to_meet', $user->id)
            ->count();

        $totalPayments = ParentInvoicePayment::query()
            ->whereHas('invoice.parentGuardian.linkedStudents', fn ($query) =>
                $query->where('student_id', $user->id)
            )
            ->sum('amount');

        return [
            Stat::make(__('Active Classes'), $totalEnrollments)
                ->description(__('Your current active enrollments'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make(__('Completed Courses'), $completedCourses)
                ->description(__('Courses completed'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('Pending Assignments'), $pendingAssignments)
                ->description(__('Assignments waiting for submission'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingAssignments > 0 ? 'warning' : 'success'),

            Stat::make(__('School Visits'), $studentVisits)
                ->description(__('Recorded reception visits'))
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),

            Stat::make(
                __('Invoice Payments'),
                __('AF :amount', ['amount' => number_format($totalPayments, 2)])
            )
                ->description(__('Payments recorded for your invoices'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
