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
            ->where(function ($q) {
                $q->where('morning_status', true)
                    ->orWhere('afternoon_status', true);
            })->count();

        $absent = (clone $base)
            ->where('morning_status', false)
            ->where('afternoon_status', false)
            ->count();

        $partial = (clone $base)
            ->whereColumn('morning_status', '!=', 'afternoon_status')
            ->count();

        $totalStudents = User::query()
            ->where('type', 'student')
            ->count();

        $recorded = (clone $base)->count();
        $notRecorded = max(0, $totalStudents - $recorded);

        return [
            Stat::make(__('Present Today'), number_format($present))
                ->description(__('At least one attendance session marked present'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('Absent Today'), number_format($absent))
                ->description(__('Absent in both attendance sessions'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make(__('Partial Attendance'), number_format($partial))
                ->description(__('Present in one session only'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('Not Recorded'), number_format($notRecorded))
                ->description(__('Students without an attendance record today'))
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('gray'),
        ];
    }
}
