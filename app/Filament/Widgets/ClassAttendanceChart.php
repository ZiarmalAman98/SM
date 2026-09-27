<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\SchoolClass;
use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        /*
         * Attendance does not necessarily store school_class_id.
         * Prefer the direct class column when the database has it.
         * Otherwise derive the class through the existing subject relation,
         * or finally through active student enrollment.
         */
        $attendanceByClass = collect();

        if (Schema::hasColumn('attendances', 'school_class_id')) {
            $records = Attendance::query()
                ->whereDate('date', today())
                ->whereIn('school_class_id', $classIds)
                ->select('school_class_id', 'student_id', 'status')
                ->get();

            $attendanceByClass = $records->groupBy('school_class_id');
        } elseif (Schema::hasColumn('attendances', 'subject_id')) {
            $records = Attendance::query()
                ->whereDate('date', today())
                ->with('subject:id,school_class_id')
                ->get([
                    'subject_id',
                    'student_id',
                    'status',
                ]);

            foreach ($records as $record) {
                $classId = $record->subject?->school_class_id;

                if ($classId && $classIds->contains($classId)) {
                    $attendanceByClass->put(
                        $classId,
                        $attendanceByClass->get($classId, collect())->push($record)
                    );
                }
            }
        } else {
            $records = Attendance::query()
                ->whereDate('date', today())
                ->get([
                    'student_id',
                    'morning_status',
                    'afternoon_status',
                ]);

            $studentIds = $records->pluck('student_id')->unique();

            $studentClasses = DB::table('student_classes')
                ->whereIn('student_id', $studentIds)
                ->whereIn('class_id', $classIds)
                ->where('status', 'active')
                ->get(['student_id', 'class_id'])
                ->groupBy('student_id');

            foreach ($records as $record) {
                foreach ($studentClasses->get($record->student_id, collect()) as $enrollment) {
                    $attendanceByClass->put(
                        $enrollment->class_id,
                        $attendanceByClass->get($enrollment->class_id, collect())->push($record)
                    );
                }
            }
        }

        $labels = [];
        $data = [];

        foreach ($classes as $class) {
            $labels[] = $class->class_name ?: __('Class #:id', ['id' => $class->id]);

            $total = (int) ($enrolled[$class->id] ?? 0);
            $records = $attendanceByClass->get($class->id, collect());

            $presentStudents = $records
                ->filter(
                    fn ($record) => (bool) $record->status
                )
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
