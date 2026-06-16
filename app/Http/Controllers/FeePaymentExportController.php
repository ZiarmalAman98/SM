<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SchoolClass;
use App\Models\FeeType;
use App\Models\FeePayment;
use App\Models\FeeGroupAssignment;
use Morilog\Jalali\Jalalian;
use Illuminate\Http\Request;

class FeePaymentExportController extends Controller
{
    public function exportClassReport(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:school_classes,id',
            'month' => 'required|string',
            'fee_type_id' => 'nullable|exists:fee_types,id',
        ]);

        $class_id = $request->class_id;
        $month = $request->month;
        $fee_type_id = $request->fee_type_id;

        // Get class information
        $class = SchoolClass::findOrFail($class_id);

        // Get fee type information if specified
        $feeType = $fee_type_id ? FeeType::find($fee_type_id) : null;

        // Get all students in the class
        $students = User::where('type', 'student')
            ->with(['student'])
            ->whereHas('studentClasses', function ($q) use ($class_id) {
                $q->where('class_id', $class_id)->where('status', 'active');
            })
            ->get();

        // Prepare report data
        $reportData = [];
        $totalFees = 0;
        $totalPaid = 0;
        $totalBalance = 0;

        foreach ($students as $student) {
            $totalFeesForStudent = $this->sumFor($student->id, 'total_fees', $class_id, $month, $fee_type_id);
            $amountPaidForStudent = $this->sumFor($student->id, 'amount_paid', $class_id, $month, $fee_type_id);
            $balanceForStudent = max(0, $totalFeesForStudent - $amountPaidForStudent);

            $status = $this->getStatus($totalFeesForStudent, $amountPaidForStudent);

            $reportData[] = [
                'admission_no' => $student->student->admission_no ?? 'N/A',
                'student_name' => $student->name,
                'father_name'  => $student->father_name ?? '-',
                'total_fees' => $totalFeesForStudent,
                'amount_paid' => $amountPaidForStudent,
                'balance' => $balanceForStudent,
                'status' => $status,
            ];

            $totalFees += $totalFeesForStudent;
            $totalPaid += $amountPaidForStudent;
            $totalBalance += $balanceForStudent;
        }

        return view('exports.fee-payments-class-report', [
            'class' => $class,
            'month' => $month,
            'feeType' => $feeType,
            'reportData' => $reportData,
            'totalFees' => $totalFees,
            'totalPaid' => $totalPaid,
            'totalBalance' => $totalBalance,
            'reportDate' => Jalalian::fromCarbon(now())->format('Y/m/d'),
        ]);
    }

    protected function sumFor(int $studentId, string $field, int $class_id, string $month, ?int $fee_type_id): float
    {
        $q = FeePayment::query()
            ->where('student_id', $studentId)
            ->where('class_id', $class_id)
            ->where('month', $month);

        if ($fee_type_id) {
            $q->where('fee_type_id', $fee_type_id);
        }

        $fromPayments = (float) $q->sum($field);

        if ($field === 'total_fees' && $fromPayments === 0.0) {
            return $this->getAssignedFeeForClass($class_id, $fee_type_id);
        }

        return $fromPayments;
    }

    protected function getAssignedFeeForClass(int $class_id, ?int $fee_type_id): float
    {
        $assignment = FeeGroupAssignment::with(['feeGroup.feeGroupFeeTypes'])
            ->where('assignable_type', SchoolClass::class)
            ->where('assignable_id', $class_id)
            ->first();

        if (!$assignment) return 0.0;

        $feeTypes = $assignment->feeGroup->feeGroupFeeTypes;

        if ($fee_type_id) {
            $feeTypes = $feeTypes->where('fee_type_id', $fee_type_id);
        }

        return (float) $feeTypes->sum('amount');
    }

    protected function getStatus(float $totalFees, float $amountPaid): string
    {
        if ($totalFees <= 0 && $amountPaid <= 0) return __('Uninvoiced');
        if ($amountPaid <= 0) return __('Unpaid');
        if ($amountPaid < $totalFees) return __('Partial');
        return __('Paid');
    }
}
