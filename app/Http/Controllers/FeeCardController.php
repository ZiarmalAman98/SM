<?php

namespace App\Http\Controllers;

use App\Models\User;

class FeeCardController extends Controller
{
    public function show(User $student)
    {
        $settings = appReportSettings();
        // Map data -> your Blade supports either direct vars or $student
        return view('students.fee-card', [
            'student'      => $student,
            'father_name'  => $student->father_name,
            'student_name' => trim(($student->name ?? '') . ' ' . ($student->last_name ?? '')),
            'phone'        => $student->student->phone ?? $student->phone, // adjust if you store on student relation
            'settings' => $settings,
        ]);
    }
}
