<?php

namespace App\Filament\Widgets;

use App\Models\SchoolClass;
use App\Models\Attendance;
use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;

class ClassAttendanceChart extends BarChartWidget
{
    protected static ?int $sort = 5;
    protected static bool $isLazy = true;
    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('Today Attendance by Class');
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 100,
                    'ticks' => [
                        'callback' => 'function(value) { return value + "%"; }',
                    ],
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $classes = SchoolClass::query()
            ->orderBy('class_name')
            ->get(['id', 'class_name']);

        if ($classes->isEmpty()) {
            return [
                'datasets' => [[
                    'label' => __('Attendance %'),
                    'data' => [],
                ]],
                'labels' => [],
            ];
        }

        $classIds = $classes->pluck('id');

        $enrolled = DB::table('student_classes')
            ->whereIn('class_id', $classIds)
            ->where('status', 'active')
            ->select('class_id', DB::raw('COUNT(DISTINCT student_id) as total'))
            ->groupBy('class_id')
            ->pluck('total', 'class_id');

        $attendance = Attendance::query()
            ->whereDate('date', today())
            ->whereIn('school_class_id', $classIds)
            ->select(
                'school_class_id',
                'student_id',
                'morning_status',
                'afternoon_status'
            )
            ->get()
            ->groupBy('school_class_id');

        $labels = [];
        $data = [];

        foreach ($classes as $class) {
            $labels[] = $class->class_name ?: __('Class #:id', ['id' => $class->id]);

            $total = (int) ($enrolled[$class->id] ?? 0);
            $records = $attendance->get($class->id, collect());

            $presentStudents = $records
                ->filter(fn ($record) => (bool) $record->morning_status || (bool) $record->afternoon_status)
                ->pluck('student_id')
                ->unique()
                ->count();

            $data[] = $total > 0
                ? round(($presentStudents / $total) * 100, 1)
                : 0;
        }

        return [
            'datasets' => [[
                'label' => __('Attendance %'),
                'data' => $data,
            ]],
            'labels' => $labels,
        ];
    }
}
