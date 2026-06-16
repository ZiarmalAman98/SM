<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Branch;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class LeavingCertificateController extends Controller
{
    public function template(Request $request)
    {
        $student = Student::with(['user', 'studentClasses.schoolClass'])
            ->find($request->student);
            
        $branch = Branch::find($request->branch);
        $class = SchoolClass::find($request->class);
        $settings = appReportSettings();
        
        return view('students.template', compact('student', 'branch', 'class', 'request', 'settings'));
    }
    
    public function assuranceTemplate(Request $request)
    {
        $student = Student::with(['user', 'studentClasses.schoolClass'])
            ->find($request->student);
            
        $branch = Branch::find($request->branch);
        $class = SchoolClass::find($request->class);
        $settings = appReportSettings();
        
        return view('students.assurance-template', compact('student', 'branch', 'class', 'request', 'settings'));
    }
    
    public function triplicateTemplate(Request $request)
    {
        $student = Student::with([
            'user',
            'studentClasses.schoolClass',
            'user.exam_scores' => function($query) use ($request) {
                $query->with(['exam', 'subject'])
                      ->whereHas('exam', function($examQuery) use ($request) {
                          $examQuery->whereYear('date', $request->year);
                      });
            }
        ])->find($request->student);
            
        $branch = Branch::find($request->branch);
        $class = SchoolClass::find($request->class);
        
        $attendance = \App\Models\ClassAttendance::where('student_id', $request->student)
            ->where('class_id', $request->class)
            ->whereYear('date', $request->year)
            ->get()
            ->groupBy('status');
        $settings = appReportSettings();
        
        return view('students.triplicate-template', compact('student', 'branch', 'class', 'request', 'attendance', 'settings'));
    }
}
