<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Assignment;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $teacher = auth()->user();

        $subjectIds = $teacher->subjects()->pluck('id');
        $classIds = $teacher->subjects()
            ->pluck('school_class_id')
            ->unique();

        // Only students actively enrolled in classes taught by this teacher.
        $totalStudents = User::query()
            ->where('type', 'student')
            ->whereHas('studentClasses', function ($query) use ($classIds) {
                $query->whereIn('class_id', $classIds)
                    ->where('status', 'active');
            })
            ->count();

        $totalSubjects = $subjectIds->count();

        $upcomingAssignmentsCount = Assignment::query()
            ->where('teacher_id', $teacher->id)
            ->whereNotNull('deadline')
            ->where('deadline', '>', Carbon::now())
            ->count();

        $pendingSubmissions = \App\Models\AssignmentSubmission::query()
            ->whereHas('assignment', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('status', 'pending')
            ->count();

        return [
            Stat::make(__('My Students'), $totalStudents)
                ->description(__('Students actively enrolled in your classes'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make(__('Subjects Taught'), $totalSubjects)
                ->description(__('Subjects assigned to you'))
                ->descriptionIcon('heroicon-m-book-open')
                ->color('success'),

            Stat::make(__('Upcoming Assignments'), $upcomingAssignmentsCount)
                ->description(__('Your assignments with future deadlines'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($upcomingAssignmentsCount > 0 ? 'warning' : 'success'),

            Stat::make(__('Pending Submissions'), $pendingSubmissions)
                ->description(__('Student submissions waiting for review'))
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color($pendingSubmissions > 0 ? 'warning' : 'success'),
        ];
    }
}
