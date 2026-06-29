<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Models\ParentGuardian;
use Illuminate\Http\Request;
use App\Models\ExamResult;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function generateIdCard(User $student)
    {
        $settings = appReportSettings();

        return view('students.shifa-card', ['user' => $student, "settings" => $settings]);
    }


    public function printExamResult(Request $request)
    {
        $studentId = $request->query('student_id');
        $classId = $request->query('class_id');

        // Fetch student with their classes (adjust relation names as per your models)
        $student = User::with(['studentClasses', 'student'])->findOrFail($studentId);
        $class = SchoolClass::findOrFail($classId);

        // Fetch exam results with related subject and exam
        $examResults = ExamResult::with(['subject', 'exam'])
            ->where('student_id', $studentId)
            ->where('class_id', $classId)
            ->get();

        // Group results by subject and exam type (mid_term, final)
        $groupedResults = [];

        foreach ($examResults as $result) {
            if (!$result->subject || !$result->exam) {
                continue; // skip if relations missing
            }

            $subjectName = $result->subject->name;
            $examType = strtolower($result->exam->exam_type); // expects 'mid_term' or 'final'

            // Normalize keys and skip unknown exam types
            if (!in_array($examType, ['mid_term', 'final'])) {
                continue;
            }

            if (!isset($groupedResults[$subjectName])) {
                $groupedResults[$subjectName] = [
                    'name' => $subjectName,
                    'mid_term' => 0,
                    'final' => 0,
                    'total' => 0,
                ];
            }

            $groupedResults[$subjectName][$examType] = $result->marks;
        }

        // Calculate totals
        $midTotal = 0;
        $finalTotal = 0;

        foreach ($groupedResults as &$subject) {
            $subject['mid_term'] = $subject['mid_term'] ?? 0;
            $subject['final'] = $subject['final'] ?? 0;
            $subject['total'] = $subject['mid_term'] + $subject['final'];

            $midTotal += $subject['mid_term'];
            $finalTotal += $subject['final'];
        }
        unset($subject);

        $subjectCount = count($groupedResults);
        $midTotalMax = $subjectCount * 40;
        $finalTotalMax = $subjectCount * 60;
        $overallTotalMax = $midTotalMax + $finalTotalMax;

        // Determine result status (fail if total per subject is below 40)
        $resultTotal = $midTotal + $finalTotal;
        $overallPercentage = $overallTotalMax > 0
            ? round(($resultTotal / $overallTotalMax) * 100, 2)
            : 0;

        $hasFailedSubject = false;
        foreach ($groupedResults as $subject) {
            if (($subject['total'] ?? 0) < 40) {
                $hasFailedSubject = true;
                break;
            }
        }

        $status = $hasFailedSubject ? 'ناکام' : 'کامیاب';

        // Dynamic grade calculation from grade_systems table using overall percentage
        $gradeRecord = DB::table('grade_systems')
            ->where('from', '<=', $overallPercentage)
            ->where('to', '>=', $overallPercentage)
            ->first();

        $grade = $gradeRecord ? $gradeRecord->title : 'نامعلوم';

        // Sample attendance data (replace with real DB data if available)
        $attendance = [
            'mid_term' => ['present' => 108, 'absent' => 2, 'leave' => null, 'sick' => null],
            'final' => ['present' => 108, 'absent' => 2, 'leave' => null, 'sick' => null],
        ];

        // Final results summary
        $result = [
            'mid_term' => $midTotal,
            'final' => $finalTotal,
            'total' => $resultTotal,
            'percentage' => $overallPercentage,
            'status' => $status,
            'grade' => $grade,
        ];

        $settings = appReportSettings();
        $parentGuardian = ParentGuardian::query()
            ->whereHas('linkedStudents', fn ($query) => $query->where('student_id', $studentId))
            ->first();

        return view('students.exam-results-1', [
            'user' => $student,
            'class' => $class,
            'subjects' => array_values($groupedResults),
            'attendance' => $attendance,
            'result' => $result,
            'familyCode' => $parentGuardian?->family_code ?? ($student->student?->admission_no ?? '---'),
            'settings' => $settings,
        ]);
    }


    public function printSubmissions(Request $request)
    {
        $query = AssignmentSubmission::with(['assignment', 'student']);

        if ($request->assignment_id) {
            $query->where('assignment_id', $request->assignment_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $submissions = $query->get();

        $assignment = Assignment::find($request->assignment_id);
        $settings = appReportSettings();
        return view('students.assignment-submissions', [
            'submissions' => $submissions,
            'assignment' => $assignment,
            'settings' => $settings,
            'filters' => $request->only('assignment_id', 'status'),
        ]);
    }


    public function printTimetable($id)
    {
        $class = SchoolClass::with([
            'subjects.teacher',
            'subjects.schedules',
        ])->findOrFail($id);
        $subjects = $class->subjects()
            ->with([
                'schedules' => function ($query) {
                    $query->orderBy('day_of_week')->orderBy('start_time');
                }
            ])
            ->get();

        $settings = appReportSettings();

        return view('students.timetable', compact('class', 'subjects', 'settings'));
    }
}
