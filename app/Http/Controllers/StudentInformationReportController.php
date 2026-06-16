<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Branch;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class StudentInformationReportController extends Controller
{
    public function generate(Request $request)
    {
        $branchId = $request->get('branch_id');
        $classId = $request->get('class_id');
        $status = $request->get('status', 'active');
        $gender = $request->get('gender');
        $reportType = $request->get('report_type', 'detailed');

        // Get branch and class information
        $branch = Branch::findOrFail($branchId);
        $class = SchoolClass::findOrFail($classId);

        // Build query for students
        $query = User::where('type', 'student')
            ->whereHas('studentClasses', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });

        // Apply status filter
        if ($status !== 'all') {
            $query->whereHas('studentClasses', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        // Apply gender filter
        if ($gender) {
            $query->where('gender', $gender);
        }

        $students = $query->with(['studentClasses', 'student'])->get();

        $appSettings = appReportSettings();

        return view('print.student-information-report', [
            'students' => $students,
            'branch' => $branch,
            'class' => $class,
            'status' => $status,
            'gender' => $gender,
            'reportType' => $reportType,
            'settings' => $appSettings,
            'schoolName' => $appSettings['app_name'],
            'appLogo' => $appSettings['app_logo'] ?? null,
            'appLogoUrl' => $appSettings['app_logo_url'],
            'schoolAddress' => $appSettings['address'],
            'generatedDateJalali' => Jalalian::fromCarbon(now()),
        ]);
    }
}
