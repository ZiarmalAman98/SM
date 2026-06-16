<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Income;
use App\Models\Expense;
use App\Models\ParentInvoicePayment;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function overview()
    {
        $totalStudents = User::where('type', 'student')->count();
        $totalTeachers = User::where('type', 'teacher')->count();
        $totalStaff = User::where('type', 'staff')->count();

        $totalMaleStudents = User::where('type', 'student')
            ->whereHas('student', fn($q) => $q->where('gender', 'male'))
            ->count();

        $totalFemaleStudents = User::where('type', 'student')
            ->whereHas('student', fn($q) => $q->where('gender', 'female'))
            ->count();

        $totalMaleEmp = User::where('type', 'staff')
            ->whereHas('staff', fn($q) => $q->where('gender', 'male'))
            ->count();

        $totalFemaleEmp = User::where('type', 'staff')
            ->whereHas('staff', fn($q) => $q->where('gender', 'female'))
            ->count();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $totalClasses = SchoolClass::count();
        $totalSections = Section::count();

        $totalApprovedIncome = Income::where('status', 'approved')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $totalApprovedExpenses = Expense::where('status', 'approved')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $totalInvoicePayments = ParentInvoicePayment::whereMonth('payment_date', $currentMonth)
            ->whereYear('payment_date', $currentYear)
            ->sum('amount');

        return response()->json([
            'students' => $totalStudents,
            'teachers' => $totalTeachers,
            'staff' => $totalStaff,
            'male_students' => $totalMaleStudents,
            'female_students' => $totalFemaleStudents,
            'male_employees' => $totalMaleEmp,
            'female_employees' => $totalFemaleEmp,
            'classes' => $totalClasses,
            'sections' => $totalSections,
            'approved_income' => $totalApprovedIncome,
            'approved_expenses' => $totalApprovedExpenses,
            'invoice_payments' => $totalInvoicePayments,
        ]);
    }
}
