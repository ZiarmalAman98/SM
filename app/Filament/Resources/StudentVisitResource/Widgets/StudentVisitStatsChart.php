<?php

namespace App\Filament\Resources\StudentVisitResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentVisitStatsChart extends BaseWidget
{

    protected function getStats(): array
    {
        // Get the current date for comparisons
        $today = Carbon::today();

        // Calculate the start of the current week (Friday)
        // Friday is day 5 of the week in Carbon, so we subtract days to get to Friday
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::FRIDAY);  // Set Friday as the start of the week

        // Start of the current month
        $startOfMonth = Carbon::now()->startOfMonth();

        // Daily Reception Forms (today)
        $dailyCount = DB::table('visitor_logs')
            ->whereDate('created_at', $today) // Filter by today's date
            ->count();

        // Total Reception Forms This Week (Starting from Friday)
        $weeklyCount = DB::table('visitor_logs')
            ->whereBetween('created_at', [$startOfWeek->startOfDay(), Carbon::now()->endOfDay()]) // Filter for current week starting from Friday
            ->count();

        // Total Reception Forms This Month
        $monthlyCount = DB::table('visitor_logs')
            ->whereBetween('created_at', [$startOfMonth->startOfDay(), Carbon::now()->endOfDay()]) // Filter for current month
            ->count();

        // Return the stats for display
        return [
            Stat::make('Daily Visitors', $dailyCount)
            ->label(__('Daily Visitors')),
            Stat::make('Total Visitors This Week', $weeklyCount)
            ->label(__('Total Visitors This Week')),
            Stat::make('Total Visitors This Month', $monthlyCount)
            ->label(__('Total Visitors This Month')),
        ];
    }

}
