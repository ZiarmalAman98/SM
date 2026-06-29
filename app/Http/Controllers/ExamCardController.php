<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ParentGuardian;
use App\Models\ParentInvoice;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;

class ExamCardController extends Controller
{
    public function show(Request $request)
    {
        $validated = $request->validate([
            'class_id' => ['required', 'exists:school_classes,id'],
            'student_id' => ['required', 'exists:users,id'],
            'exam_type' => ['required', 'in:mid_term,final'],
            'exam_id' => ['nullable', 'exists:exams,id'],
        ]);

        $student = User::query()
            ->with(['student', 'studentClasses.schoolClass'])
            ->where('type', 'student')
            ->findOrFail($validated['student_id']);

        $schoolClass = SchoolClass::findOrFail($validated['class_id']);

        $exam = Exam::query()
            ->when(
                filled($validated['exam_id'] ?? null),
                fn ($query) => $query->whereKey($validated['exam_id']),
                fn ($query) => $query
                    ->where('class_id', $schoolClass->id)
                    ->where('exam_type', $validated['exam_type'])
                    ->orderByDesc('date')
                    ->orderByDesc('id')
            )
            ->first();

        $parentGuardian = ParentGuardian::query()
            ->with('user')
            ->whereHas('linkedStudents', fn ($query) => $query->where('student_id', $student->id))
            ->first();

        $invoiceQuery = ParentInvoice::query()
            ->with(['items.feeType', 'payments'])
            ->whereHas('items', fn ($query) => $query->where('student_id', $student->id))
            ->orderByDesc('invoice_date')
            ->orderByDesc('id');

        if ($parentGuardian) {
            $invoiceQuery->where('parent_guardian_id', $parentGuardian->id);
        }

        $invoices = $invoiceQuery->get();
        $latestInvoice = $invoices->first();

        $studentItems = $invoices
            ->flatMap(fn ($invoice) => $invoice->items)
            ->where('student_id', $student->id)
            ->values();

        $latestPayment = $invoices
            ->flatMap(fn ($invoice) => $invoice->payments)
            ->sortByDesc(fn ($payment) => optional($payment->payment_date)->timestamp ?? 0)
            ->sortByDesc('id')
            ->first();

        $bucketTotal = function (array $terms) use ($studentItems): float {
            return (float) $studentItems
                ->filter(function ($item) use ($terms) {
                    $name = strtolower(trim(($item->feeType?->name ?? '') . ' ' . ($item->description ?? '')));

                    foreach ($terms as $term) {
                        if (str_contains($name, $term)) {
                            return true;
                        }
                    }

                    return false;
                })
                ->sum('amount');
        };

        $admissionTotal = $bucketTotal(['admission', 'registration']);
        $equipmentTotal = $bucketTotal(['equipment', 'uniform', 'stationery', 'material', 'oxford']);
        $monthlyTotal = $bucketTotal(['monthly', 'tuition', 'month']);
        $transportTotal = $bucketTotal(['transport']);
        $studentTotal = (float) $studentItems->sum('amount');

        $periods = $invoices
            ->map(fn ($invoice) => trim(($invoice->billing_month ?? '-') . ' ' . ($invoice->billing_year ?? '')))
            ->filter()
            ->unique()
            ->take(4)
            ->values();

        $settings = appReportSettings();
        $examDate = $exam?->date ? Jalalian::fromDateTime($exam->date)->format('Y/m/d') : '-';
        $printedDate = Jalalian::now()->format('Y/m/d');
        $receiptNo = $latestPayment?->receipt_number ?? ($latestInvoice?->invoice_number ?? '-');
        $cardCode = $latestInvoice?->family_code
            ?? $parentGuardian?->family_code
            ?? ($student->student?->admission_no ?: '-');
        $examLabel = $validated['exam_type'] === 'final' ? 'Final Examination' : 'Mid Term Examination';

        return view('print.exam-card', [
            'settings' => $settings,
            'student' => $student,
            'schoolClass' => $schoolClass,
            'exam' => $exam,
            'examLabel' => $examLabel,
            'examDate' => $examDate,
            'printedDate' => $printedDate,
            'receiptNo' => $receiptNo,
            'cardCode' => $cardCode,
            'admissionTotal' => $admissionTotal,
            'equipmentTotal' => $equipmentTotal,
            'monthlyTotal' => $monthlyTotal,
            'transportTotal' => $transportTotal,
            'studentTotal' => $studentTotal,
            'receivedAmount' => (float) ($latestPayment?->amount ?? ($latestInvoice?->paid_amount ?? 0)),
            'periods' => $periods,
        ]);
    }
}
