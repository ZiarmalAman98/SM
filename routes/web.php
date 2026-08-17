<?php

use App\Models\Income;
use App\Models\Question;
use App\Models\IncomeSource;
use Illuminate\Http\Request;
use App\Models\ClassAttendance;
use App\Models\EvaluationResponse;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Exports\ClassAttendanceExport;
use App\Http\Controllers\FeeCardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExamResultController;
use App\Http\Controllers\WordDesignController;
use App\Http\Controllers\NaqalEMakanController;
use App\Http\Controllers\PayrollPrintController;
use App\Http\Controllers\BiographyCardController;
use App\Http\Controllers\StudentLetterController;
use App\Http\Controllers\BiographyPrintController;
use App\Http\Controllers\ExamCardController;
use App\Http\Controllers\ExamResultPrintController;
use App\Http\Controllers\FormattedResultsController;
use App\Http\Controllers\ExamResultsReportController;
use App\Http\Controllers\FinancialSummaryReportController;
use App\Http\Controllers\StudentInformationReportController;




Route::get('/', function () {
    return view('welcome');
});

Route::get('/export/class-attendance', function () {
    $records = ClassAttendance::with(['student', 'class', 'branch'])->latest()->get();
    return Excel::download(new ClassAttendanceExport($records), 'class_attendance_report.xlsx');
})->name('export.class.attendance');

Route::get('/print-incomes', function (Request $request) {
    $query = Income::query();

    if ($request->filled('from') && $request->filled('to')) {
        $query->whereBetween('date', [$request->from, $request->to]);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $incomes = $query->with('source')->orderBy('date')->get();

    // ✅ This is the missing part
    $sources = IncomeSource::all();
    $settings = appReportSettings();

    return view('print.income', compact('incomes', 'sources', 'settings'));
})->name('income.print');

Route::get('/exam/print', function (Request $request) {
    // Extract parameters with default values
    $examName = $request->input('exam_name', 'Sample Exam');
    $teacherName = $request->input('teacher_name', 'Unknown Teacher');
    $examDate = $request->input('exam_date', now()->toDateString());
    $duration = $request->input('duration', 60);
    $totalScore = $request->input('total_score', 100);
    $subjectId = $request->input('subject_id');
    $difficultyId = $request->input('difficulty_id');
    $languageId = $request->input('language_id');

    // Decode JSON question types
    $questionTypes = json_decode($request->input('question_types', '[]'), true);

    $groupedQuestions = [];
    $questionScores = []; // Separate array for scores

    // Fetch questions for each type
    foreach ($questionTypes as $typeData) {
        $type = $typeData['type'];
        $amount = (int) $typeData['amount'];
        $score = isset($typeData['score']) ? $typeData['score'] : '';

        $query = Question::query();

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        if ($difficultyId) {
            $query->where('difficulty_id', $difficultyId);
        }
        if ($languageId) {
            $query->where('language_id', $languageId);
        }

        $questions = $query->where('type', $type)->inRandomOrder()->limit($amount)->get();

        // If not enough questions, fill the gap with random ones
        if ($questions->count() < $amount) {
            $remaining = $amount - $questions->count();
            $extraQuestions = Question::where('type', $type)->inRandomOrder()->limit($remaining)->get();
            $questions = $questions->merge($extraQuestions);
        }

        $groupedQuestions[$type] = $questions;
        $questionScores[$type] = $score;
    }

    // dd($groupedQuestions);

    $settings = appReportSettings();

    // Pass data to the view
    return view('exams.print', [
        'exam_name' => $examName,
        'teacher_name' => $teacherName,
        'exam_date' => $examDate,
        'duration' => $duration,
        'total_score' => $totalScore,
        'grouped_questions' => $groupedQuestions,
        'question_scores' => $questionScores,
        'settings' => $settings,
    ]);
})->name('exam.print');

Route::get('/evaluation-report/print', function (Request $request) {
    $query = EvaluationResponse::query()->with(['teacher', 'student', 'question']);

    // Apply filters
    if ($request->filled('teacher_id')) {
        $query->where('teacher_id', $request->teacher_id);
    }

    if ($request->filled('student_id')) {
        $query->where('student_id', $request->student_id);
    }

    if ($request->filled('academic_year')) {
        $query->where('academic_year', $request->academic_year);
    }

    $responses = $query->orderBy('teacher_id')->get();

    $settings = appReportSettings();


    return view('print.evaluations', [
        'responses' => $responses,
        'settings' => $settings,
    ]);
})->name('evaluation.print');

Route::get('/payments/print/{payment}', \App\Http\Controllers\PrintPaymentController::class)->name('print.payment');

Route::get('/parent-invoices/{parentInvoice}/print', function (\App\Models\ParentInvoice $parentInvoice) {
    $parentInvoice->load([
        'parentGuardian.user',
        'items.student',
        'items.schoolClass',
        'items.feeType',
        'payments',
    ]);

    $settings = appReportSettings();

    return view('print.parent-invoice', [
        'invoice' => $parentInvoice,
        'settings' => $settings,
    ]);
})->name('parent-invoices.print');

Route::get('/parent-invoice-payments/{parentInvoicePayment}/print', function (\App\Models\ParentInvoicePayment $parentInvoicePayment) {
    $parentInvoicePayment->load([
        'invoice.parentGuardian.user',
        'invoice.items.student',
        'invoice.items.schoolClass',
        'invoice.items.feeType',
        'invoice.payments',
    ]);

    $settings = appReportSettings();

    return view('print.parent-invoice-payment', [
        'payment' => $parentInvoicePayment,
        'settings' => $settings,
    ]);
})->name('parent-invoice-payments.print');

Route::get('/inventory-sales/{inventorySale}/print', function (\App\Models\InventorySale $inventorySale) {
    $inventorySale->load([
        'student',
        'parentGuardian.user',
        'items.product',
        'payments',
    ]);

    $settings = appReportSettings();

    return view('print.inventory-sale', [
        'sale' => $inventorySale,
        'settings' => $settings,
    ]);
})->name('inventory-sales.print');

Route::get('/inventory-purchases/{inventoryPurchase}/print', function (\App\Models\InventoryPurchase $inventoryPurchase) {
    $inventoryPurchase->load([
        'supplier',
        'items.product',
    ]);

    $settings = appReportSettings();

    return view('print.inventory-purchase', [
        'purchase' => $inventoryPurchase,
        'settings' => $settings,
    ]);
})->name('inventory-purchases.print');

Route::get('/reports/parent-invoice-payments/print', function (Request $request) {
    $query = \App\Models\ParentInvoicePayment::query()
        ->with(['invoice.parentGuardian.user'])
        ->whereBetween('payment_date', [
            $request->date('from_date')?->startOfDay() ?? now()->startOfMonth(),
            $request->date('to_date')?->endOfDay() ?? now()->endOfDay(),
        ])
        ->when($request->filled('payment_method'), fn($query) => $query->where('payment_method', $request->payment_method))
        ->when($request->filled('parent_guardian_id'), function ($query) use ($request) {
            $query->whereHas('invoice', fn($query) => $query->where('parent_guardian_id', $request->integer('parent_guardian_id')));
        })
        ->orderBy('payment_date')
        ->orderBy('receipt_number');

    $settings = appReportSettings();

    return view('print.parent-invoice-payments-report', [
        'payments' => $query->get(),
        'settings' => $settings,
        'fromDate' => $request->date('from_date') ?? now()->startOfMonth(),
        'toDate' => $request->date('to_date') ?? now(),
    ]);
})->name('parent-invoice-payments.report');


Route::get('/payrolls/print/{id}', [PayrollPrintController::class, 'print'])->name('payrolls.print');
Route::get('/students/{student}/id-card', [StudentController::class, 'generateIdCard'])->name('students.id-card');
Route::get('/exam-result/print', [StudentController::class, 'printExamResult'])->name('examResult.print');
Route::get('/assignments/submissions/print', [StudentController::class, 'printSubmissions'])
    ->name('assignments.submissions.print');
Route::get('/classes/{id}/print-timetable', [StudentController::class, 'printTimetable'])->name('classes.print-timetable');
Route::get('/student-classes/{studentClass}/print-letter', [StudentLetterController::class, 'fromStudentClass'])
    ->name('student-classes.print-letter');

// keep this too if you also print directly from a Student row
Route::get('/students/{student}/print-letter', [StudentLetterController::class, 'fromStudent'])
    ->name('students.print-letter');

Route::get('/word/shuqa/download', [WordDesignController::class, 'downloadFromTemplate']);
Route::get('/word/design/build',   [WordDesignController::class, 'buildFromScratch']);

Route::get('/students/{student}/fee-card', [FeeCardController::class, 'show'])
    ->name('students.fee-card');


Route::get('/exam-results/print/subject', [ExamResultPrintController::class, 'subjectByClass'])
    ->name('examResults.subject.print');

Route::get('/biographies/{biography}/print', BiographyPrintController::class)
    ->name('biographies.print');

Route::get('/biographies/{biography}/card', BiographyCardController::class)
    ->name('biographies.card');

Route::get('/visitor-logs/{visitorLog}/print', function (\App\Models\VisitorLog $visitorLog) {
    $visitorLog->load('personToMeet.roles');

    $settings = appReportSettings();

    return view('print.visitor-log', [
        'visitor' => $visitorLog,
        'settings' => $settings,
    ]);
})->name('visitor-logs.print');

Route::get('/complaints/{complaint}/print', function (\App\Models\Complaint $complaint) {
    $complaint->load(['complainant', 'correspondent']);

    $settings = appReportSettings();

    return view('print.complaint', [
        'complaint' => $complaint,
        'settings' => $settings,
    ]);
})->name('complaints.print');

Route::get('/inventory-suppliers/{inventorySupplier}/ledger', function (\App\Models\InventorySupplier $inventorySupplier) {
    $purchases = $inventorySupplier->purchases()
        ->where('status', '!=', 'cancelled')
        ->orderBy('purchase_date')
        ->orderBy('id')
        ->get();

    $settings = appReportSettings();

    return view('print.supplier-ledger', [
        'supplier' => $inventorySupplier,
        'purchases' => $purchases,
        'settings' => $settings,
    ]);
})->name('inventory-suppliers.ledger');

Route::get('/student-information-report/generate', [StudentInformationReportController::class, 'generate'])
    ->name('student-information-report.generate');

Route::get('/financial-summary-report/generate', [FinancialSummaryReportController::class, 'generate'])
    ->name('financial-summary-report.generate');

Route::get('/exam-results-report/generate', [ExamResultsReportController::class, 'generate'])
    ->name('exam-results-report.generate');

Route::get('/exam-card/print', [ExamCardController::class, 'show'])
    ->name('exam-card.print');

Route::get('/download/formatted-results', [FormattedResultsController::class, 'download'])
    ->name('download.formatted.results');



// Route::get('/export-results', [\App\Http\Controllers\ResultsExportController::class, 'export']);






//////////////
// use App\Exports\SchoolResultsExport;
// use App\Models\Student;

// Route::get('/export-school-results', function () {
//     $students = Student::all(); // Or a custom query, or a Collection

//     return Excel::download(new SchoolResultsExport($students), 'نتایج-صنوف-1404.xlsx');
// });

Route::get('/appreciation-letter', function (Request $request) {
    $studentName = $request->input('student_name', '..........');
    $fatherName = $request->input('father_name', '..........');

    $settings = appReportSettings();

    return view('letters.appreciation', compact('studentName', 'fatherName', 'settings'));
})->name('appreciation.letter');

Route::get('/leaving-certificate/template', [App\Http\Controllers\LeavingCertificateController::class, 'template'])
    ->name('leaving-certificate.template');

Route::get('/assurance-letter/template', [App\Http\Controllers\LeavingCertificateController::class, 'assuranceTemplate'])
    ->name('assurance-letter.template');

Route::get('/triplicate-form/template', [App\Http\Controllers\LeavingCertificateController::class, 'triplicateTemplate'])
    ->name('triplicate-form.template');

Route::get('/naqal-e-makan/print', [NaqalEMakanController::class, 'print'])
    ->name('naqal-e-makan.print');
