<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use App\Models\ExamResult;
use Illuminate\Http\Request;

class ExamResultPrintController extends Controller
{
    /**
     * Print one subject result of a specific class.
     * Query params: branch_id, class_id, subject_id, exam_type
     */
    public function subjectByClass(Request $request)
    {
        $validated = $request->validate([
            'branch_id'  => ['required', 'integer', 'exists:branches,id'],
            'class_id'   => ['required', 'integer', 'exists:school_classes,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'exam_type'  => ['required', 'string', 'in:mid_term,final'],
        ]);

        $class    = SchoolClass::with('branch')->findOrFail($validated['class_id']);
        $subject  = Subject::findOrFail($validated['subject_id']);
        $examType = $validated['exam_type'];

        // Active students in this class
        $students = User::whereHas('studentClasses', function ($q) use ($class) {
            $q->where('class_id', $class->id)->where('status', 'active');
        })
            ->with('student') // if you need student_code, etc.
            ->orderBy('name')
            ->get();

        // ✅ Filter ONLY by the selected exam type
        $results = ExamResult::with(['exam', 'subject'])
            ->where('class_id', $class->id)
            ->where('subject_id', $subject->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->whereHas('exam', fn($q) => $q->where('exam_type', $examType)) // <-- filter here
            ->get()
            ->groupBy('student_id');

        return view('exams.subject-print', [
            'branch'    => $class->branch ?? null,
            'class'     => $class,
            'subject'   => $subject,
            'students'  => $students,
            'results'   => $results,
            'examType'  => $examType,
            'settings'  => appReportSettings(),
        ]);
    }
}
