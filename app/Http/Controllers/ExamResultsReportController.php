<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamType;
use App\Models\User;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class ExamResultsReportController extends Controller
{
    public function generate(Request $request)
    {
        $branchId = $request->get('branch_id');
        $classId = $request->get('class_id');
        $examType = $request->get('exam_type', 'mid_term');
        $academicYear = $request->get('academic_year');

        $branch = Branch::findOrFail($branchId);
        $class = $classId ? SchoolClass::findOrFail($classId) : null;

        $examData = $this->getExamResultsData($branchId, $classId, $examType, $academicYear);

        $appSettings = appReportSettings();

        return view('print.exam-results-report', [
            'branch' => $branch,
            'class' => $class,
            'examType' => $examType,
            'academicYear' => $academicYear,
            'examData' => $examData,
            'settings' => $appSettings,
            'schoolName' => $appSettings['app_name'],
            'appLogo' => $appSettings['app_logo'] ?? null,
            'appLogoUrl' => $appSettings['app_logo_url'],
            'schoolAddress' => $appSettings['address'],
            'generatedDateJalali' => Jalalian::fromCarbon(now()),
        ]);
    }

    private function getExamResultsData($branchId, $classId, $examType, $academicYear)
    {
        $classResults = [];
        $totalStats = [
            'total_students' => 0,
            'male_total' => 0,
            'female_total' => 0,
            'present_students' => 0,
            'absent_students' => 0,
            'pass_students' => 0,
            'fail_students' => 0,
            'male_present' => 0,
            'female_present' => 0,
            'male_pass' => 0,
            'female_pass' => 0,
            'male_more_effort' => 0,
            'female_more_effort' => 0,
            'male_excused' => 0,
            'female_excused' => 0,
            'male_absent' => 0,
            'female_absent' => 0,
            'more_effort' => 0,
        ];

        // Get all exam results first to see what we actually have
        $allExamResults = ExamResult::with(['class', 'student'])->get();

        // Group exam results by class
        $examResultsByClass = $allExamResults->groupBy('class_id');

        // Get all classes that have exam results
        $classesWithResults = SchoolClass::whereIn('id', $examResultsByClass->keys())->get();

        foreach ($classesWithResults as $class) {
            $classExamResults = $examResultsByClass->get($class->id, collect());

            // Get students who have exam results for this class
            $studentsWithResults = User::whereIn('id', $classExamResults->pluck('student_id'))
                ->where('type', 'student')
                ->with(['student'])
                ->get();

            $classStats = [
                'class_name' => $class->class_name,
                'total_students' => $studentsWithResults->count(),
                'male_total' => 0,
                'female_total' => 0,
                'present_students' => 0,
                'absent_students' => 0,
                'pass_students' => 0,
                'fail_students' => 0,
                'male_present' => 0,
                'female_present' => 0,
                'male_pass' => 0,
                'female_pass' => 0,
                'male_more_effort' => 0,
                'female_more_effort' => 0,
                'male_excused' => 0,
                'female_excused' => 0,
                'male_absent' => 0,
                'female_absent' => 0,
                'more_effort' => 0,
                'excused_absent' => 0,
            ];

            // Process only students who have exam results
            foreach ($studentsWithResults as $student) {
                $gender = $student->student?->gender ?? 'male';

                // Count total students by gender
                if ($gender === 'male' || $gender === 'Male' || $gender === 'M' || $gender === '1') {
                    $classStats['male_total']++;
                } else {
                    $classStats['female_total']++;
                }

                // Get this student's exam results for this class
                $studentResults = $classExamResults->where('student_id', $student->id);

                if ($studentResults->count() > 0) {
                    $classStats['present_students']++;
                    if ($gender === 'male' || $gender === 'Male' || $gender === 'M' || $gender === '1') {
                        $classStats['male_present']++;
                    } else {
                        $classStats['female_present']++;
                    }

                    // Check if student passed based on exam type
                    // Based on StudentController: pass threshold is 40 marks for both exam types
                    // This means: mid-term (40/40 = 100%), final (40/60 = 66.7%)
                    $hasPassed = false;

                    if ($examType === 'mid_term') {
                        // For mid-term: pass if marks >= 40 (out of max 40)
                        $hasPassed = $studentResults->every(function ($result) {
                            return $result->marks >= 40;
                        });
                    } else if ($examType === 'final') {
                        // For final: pass if marks >= 40 (out of max 60)
                        $hasPassed = $studentResults->every(function ($result) {
                            return $result->marks >= 40;
                        });
                    }

                    if ($hasPassed) {
                        $classStats['pass_students']++;
                        if ($gender === 'male' || $gender === 'Male' || $gender === 'M' || $gender === '1') {
                            $classStats['male_pass']++;
                        } else {
                            $classStats['female_pass']++;
                        }
                    } else {
                        $classStats['fail_students']++;
                        // Add to "more effort" category for failed students
                        $classStats['more_effort']++;
                        if ($gender === 'male' || $gender === 'Male' || $gender === 'M' || $gender === '1') {
                            $classStats['male_more_effort']++;
                        } else {
                            $classStats['female_more_effort']++;
                        }
                    }
                }
            }

            // Only add class if it has exam results
            if ($classExamResults->count() > 0) {
                // Update totals
                $totalStats['total_students'] += $classStats['total_students'];
                $totalStats['male_total'] += $classStats['male_total'];
                $totalStats['female_total'] += $classStats['female_total'];
                $totalStats['present_students'] += $classStats['present_students'];
                $totalStats['absent_students'] += $classStats['absent_students'];
                $totalStats['pass_students'] += $classStats['pass_students'];
                $totalStats['fail_students'] += $classStats['fail_students'];
                $totalStats['male_present'] += $classStats['male_present'];
                $totalStats['female_present'] += $classStats['female_present'];
                $totalStats['male_pass'] += $classStats['male_pass'];
                $totalStats['female_pass'] += $classStats['female_pass'];
                $totalStats['male_more_effort'] += $classStats['male_more_effort'];
                $totalStats['female_more_effort'] += $classStats['female_more_effort'];
                $totalStats['male_excused'] += $classStats['male_excused'];
                $totalStats['female_excused'] += $classStats['female_excused'];
                $totalStats['male_absent'] += $classStats['male_absent'];
                $totalStats['female_absent'] += $classStats['female_absent'];
                $totalStats['more_effort'] += $classStats['more_effort'];

                $classResults[] = $classStats;
            }
        }

        return [
            'classResults' => $classResults,
            'totalStats' => $totalStats,
            'examTypeName' => $this->getExamTypeName($examType),
        ];
    }

    private function getExamTypeName($examType)
    {
        $types = [
            'mid_term' => 'چهارنیم ماه',
            'final' => 'سالانه',
        ];

        return $types[$examType] ?? 'امتحان';
    }

    private function convertExamTypeToDb($examType)
    {
        return $examType;
    }
}
