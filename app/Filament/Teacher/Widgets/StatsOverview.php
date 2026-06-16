<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Assignment;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $teacher = auth()->user();

        // Total Number of Students Assigned to the Teacher (across all classes)
        $totalStudents = User::where('type', 'student')->count();

        // Total Number of subjects Taught by the Teacher
        $totalClasses = $teacher->subjects()->count();

        $upcomingAssignmentsCount = Assignment::where('teacher_id', $teacher->id)
            ->where('deadline', '>', Carbon::now())
            ->count();

        return [
            Stat::make(__('Total Students'), $totalStudents)
                ->description(__('Total number of students assigned to your classes'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(__('Total Classes'), $totalClasses)
                ->description(__('Total number of classes you are teaching'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(__('Upcoming Assignments'), $upcomingAssignmentsCount)
                ->description(__('Assignments with deadlines in the future'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),
        ];
    }
}