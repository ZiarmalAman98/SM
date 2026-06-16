<?php

namespace App\Filament\Student\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceChart extends ChartWidget
{
    public function getHeading(): string
    {
        return __('Your Attendance');
    }
    
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $user = Auth::user();

        // Get active student class IDs
        $studentClassIds = DB::table('student_classes')
            ->where('student_id', $user->id)
            ->where('status', 'active')
            ->pluck('class_id');

        if ($studentClassIds->isEmpty()) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        // Get subject IDs for current classes
        $subjects = DB::table('subjects')
            ->whereIn('school_class_id', $studentClassIds)
            ->pluck('name', 'id');

        if ($subjects->isEmpty()) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        // Fetch attendance grouped by subject + month
        $attendanceData = DB::table('attendances')
            ->where('student_id', $user->id)
            ->whereIn('subject_id', $subjects->keys())
            ->selectRaw('subject_id, MONTH(date) as month, COUNT(*) as total, SUM(status) as present')
            ->groupBy('subject_id', 'month')
            ->get();

        // Prepare chart data
        $labels = [];
        $presentData = [];
        $absentData = [];

        foreach ($subjects as $subjectId => $subjectName) {
            $subjectRecords = $attendanceData->where('subject_id', $subjectId);

            $monthlyLabels = $subjectRecords->pluck('month')->map(fn($month) => __(date('F', mktime(0, 0, 0, $month, 1))));
            $labels = array_merge($labels, $monthlyLabels->toArray());

            $monthlyPresent = $subjectRecords->pluck('present');
            $monthlyTotal = $subjectRecords->pluck('total');
            $monthlyAbsent = $monthlyTotal->map(fn($total, $index) => $total - $monthlyPresent[$index]);

            $presentData[] = [
                'label' => __(":subject - Present", ['subject' => $subjectName]),
                'data' => $monthlyPresent->toArray(),
                'backgroundColor' => __('green'),
                'borderColor' => __('green'),
                'borderWidth' => 1,
            ];

            $absentData[] = [
                'label' => __(":subject - Absent", ['subject' => $subjectName]),
                'data' => $monthlyAbsent->toArray(),
                'backgroundColor' => __('red'),
                'borderColor' => __('red'),
                'borderWidth' => 1,
            ];
        }

        // Remove duplicate labels and sort by month
        $labels = array_values(array_unique($labels));
        usort($labels, fn($a, $b) => strtotime($a) - strtotime($b));

        return [
            'labels' => $labels,
            'datasets' => array_merge($presentData, $absentData),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}