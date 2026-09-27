<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\ParentInvoicePayment;
use App\Models\StudentClass;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $parent = auth()->user();

        $studentIds = $parent->children1()->pluck('student_id');

        $childrenCount = $studentIds->count();

        $activeEnrollments = StudentClass::query()
            ->whereIn('student_id', $studentIds)
            ->where('status', 'active')
            ->count();

        $pendingAssignments = Assignment::query()
            ->whereHas('subject.schoolClass.studentClasses', function ($query) use ($studentIds) {
                $query->whereIn('student_id', $studentIds)
                    ->where('status', 'active');
            })
            ->whereDoesntHave('submissions', function ($query) use ($studentIds) {
                $query->whereIn('student_id', $studentIds);
            })
            ->where(function ($query) {
                $query->whereNull('deadline')
                    ->orWhere('deadline', '>=', now());
            })
            ->count();

        $todayAbsences = Attendance::query()
            ->whereIn('student_id', $studentIds)
            ->whereDate('date', today())
            ->where('status', false)
            ->count();

        $totalPayments = ParentInvoicePayment::query()
            ->whereHas('invoice.parentGuardian', function ($query) use ($parent) {
                $query->where('user_id', $parent->id);
            })
            ->sum('amount');

        return [
            Stat::make(__('My Children'), $childrenCount)
                ->description(__('Children linked to your account'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make(__('Active Enrollments'), $activeEnrollments)
                ->description(__('Current active class enrollments'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make(__('Pending Assignments'), $pendingAssignments)
                ->description(__('Assignments waiting for your children'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingAssignments > 0 ? 'warning' : 'success'),

            Stat::make(__('Today Absences'), $todayAbsences)
                ->description(__('Absence records for your children today'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($todayAbsences > 0 ? 'danger' : 'success'),

            Stat::make(
                __('Total Payments'),
                __('AF :amount', ['amount' => number_format($totalPayments, 2)])
            )
                ->description(__('Payments recorded for your family invoices'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
