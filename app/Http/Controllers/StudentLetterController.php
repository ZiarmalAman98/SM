<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AppSetting;
use App\Models\StudentClass;
use Illuminate\Http\Request;

class StudentLetterController extends Controller
{
    // When you open from a Students table
    public function fromStudent(User $student, Request $request)
    {
        if ($student->type !== 'student') {
            abort(404);
        }

        [$studentName, $fatherName] = $this->namesFromUser($student, $request);
        $settings = AppSetting::pluck('value', 'key')->toArray();

        return view('letters.appreciation', [
            'studentName' => $studentName,
            'fatherName'  => $fatherName,
            'today'       => now()->format('Y/m/d'),
            'settings' => $settings,
        ]);
    }

    // When you open from a StudentClass (enrollment) row
    public function fromStudentClass(StudentClass $studentClass, Request $request)
    {
        $student = $studentClass->student;   // uses the relation on StudentClass

        if (!$student || $student->type !== 'student') {
            abort(404);
        }

        [$studentName, $fatherName] = $this->namesFromUser($student, $request);
        $settings = AppSetting::pluck('value', 'key')->toArray();

        return view('letters.appreciation', [
            'studentName' => $studentName,
            'fatherName'  => $fatherName,
            'today'       => now()->format('Y/m/d'),
            'settings' => $settings,
        ]);
    }

    private function namesFromUser(User $user, Request $request): array
    {
        // Resolve full name from common schema variants
        $name = $user->full_name
            ?? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
            ?: ($user->name ?? '');

        $father = $user->father_name ?? '';

        // Allow optional overrides via query params if ever needed
        $name   = $request->filled('name')   ? trim($request->string('name'))   : $name;
        $father = $request->filled('father') ? trim($request->string('father')) : $father;

        return [$name, $father];
    }
}
