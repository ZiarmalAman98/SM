<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceDashboard extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        $today = today();

        $base = Attendance::query()
            ->whereDate('date', $today);

        $present = (clone $base)
            ->where('status', true)
            ->distinct('student_id')
            ->count('student_id');

        $absent = (clone $base)
            ->where('status', false)
            ->distinct('student_id')
            ->count('student_id');

        // The current attendance schema stores one status per attendance record,
        // not separate morning/afternoon sessions. A student with both present
        // and absent records today is therefore treated as having mixed status.
        $mixedStatus = Attendance::query()
            ->whereDate('date', $today)
            ->select('student_id')
            ->groupBy('student_id')
            ->havingRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) > 0')
            ->havingRaw('SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) > 0')
            ->count();

        $totalStudents = User::query()
            ->where('type', 'student')
            ->count();

        $recorded = (clone $base)
            ->distinct('student_id')
            ->count('student_id');

        $notRecorded = max(0, $totalStudents - $recorded);

        return [
            Stat::make(__('Present Today'), number_format($present))
                ->description(__('Students with at least one present attendance record'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('Absent Today'), number_format($absent))
                ->description(__('Students with at least one absent attendance record'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make(__('Mixed Status'), number_format($mixedStatus))
                ->description(__('Students marked both present and absent across subjects today'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('Not Recorded'), number_format($notRecorded))
                ->description(__('Students without an attendance record today'))
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('gray'),
        ];
    }
}
