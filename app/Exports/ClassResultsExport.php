<?php

namespace App\Exports;

use App\Models\User;
use App\Models\ExamResult;
use App\Models\GradeSystem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ClassResultsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $classId;
    protected $className;
    protected $dataRows = []; // To store processed data for styling
    protected $studentSummaryRows = []; // To store row numbers for student summaries
    protected $overallSummaryRow = 0; // To store row number for overall summary

    // Define all column keys for consistency across all rows
    const COLUMNS = [
        'student_id', 'student_name', 'father_name', 'grandfather_name', 'subject', 'mid_term', 'final', 'total',
        'written_marks', 'recital_marks', 'homework_marks', 'class_activity_marks', 'mark_in_words',
        'attendance_mid_present', 'attendance_mid_absent', 'attendance_mid_leave', 'attendance_mid_sick',
        'attendance_final_present', 'attendance_final_absent', 'attendance_final_leave', 'attendance_final_sick',
        'student_code'
    ];

    public function __construct($classId, $className)
    {
        $this->classId = $classId;
        $this->className = $className;
    }

    public function collection(): Collection
    {
        $students = User::whereHas('studentClasses', function ($query) {
            $query->where('class_id', $this->classId)
                  ->where('status', 'active');
        })->get();

        $data = [];
        $currentRow = 1; // Start row for actual data

        // --- Report Header (mimicking HTML structure) ---
        $data[] = $this->createRow('دولت جمهوری اسلامی افغانستان', 'gov_title', $currentRow++);
        $data[] = $this->createRow('وزارت معـــــــــارف', 'ministry_title', $currentRow++);
        $data[] = $this->createRow('ریاست معارف شهر', 'directorate_title', $currentRow++);
        $data[] = $this->createRow(env('SCHOOL_NAME_FA', 'نام مکتب'), 'school_name', $currentRow++); // Use env for school name
        $data[] = $this->createRow('کارنامه تعلیمی - سال تعلیمی: ' . (Carbon::now()->year - 621), 'report_title_fa', $currentRow++); // Persian year
        $data[] = $this->createRow('تاریخ تهیه: ' . Carbon::now()->format('Y-m-d H:i:s'), 'generated_date', $currentRow++);
        $data[] = $this->createRow('', 'spacing_before_headings', $currentRow++); // Empty row for spacing

        // --- Student Data ---
        $totalStudentsPassed = 0;
        $totalOverallScore = 0;

        foreach ($students as $student) {
            $results = ExamResult::with(['exam', 'subject'])
                ->where('student_id', $student->id)
                ->where('class_id', $this->classId)
                ->get()
                ->groupBy(fn($r) => $r->subject->name ?? '-');

            $midTotal = 0;
            $finalTotal = 0;
            $studentSubjectRows = [];

            foreach ($results as $subject => $exams) {
                $midResult = $exams->where('exam.exam_type', 'mid_term')->first();
                $finalResult = $exams->where('exam.exam_type', 'final')->first();

                $mid = $midResult?->marks ?? 0;
                $final = $finalResult?->marks ?? 0;

                // Get detailed marks from the most recent result
                $currentResult = $midResult ?? $finalResult;
                $writtenMarks = $currentResult?->written_marks ?? 0;
                $recitalMarks = $currentResult?->recital_marks ?? 0;
                $homeworkMarks = $currentResult?->homework_marks ?? 0;
                $activityMarks = $currentResult?->class_activity_marks ?? 0;
                $markInWords = $currentResult?->mark_in_words ?? '';

                $studentSubjectRows[] = [
                    'student_id'   => $student->id,
                    'student_name' => $student->name,
                    'father_name' => $student->father_name ?? '',
                    'grandfather_name' => $student->grandfather_name ?? '',
                    'subject'      => $subject,
                    'mid_term'     => $mid,
                    'final'        => $final,
                    'total'        => $mid + $final,
                    'written_marks' => $writtenMarks,
                    'recital_marks' => $recitalMarks,
                    'homework_marks' => $homeworkMarks,
                    'class_activity_marks' => $activityMarks,
                    'mark_in_words' => $markInWords,
                ];
                $midTotal += $mid;
                $finalTotal += $final;
            }

            $hasFailed = collect($studentSubjectRows)->contains(fn($row) => $row['mid_term'] < 40 || $row['final'] < 40);
            $overallStudentScore = $midTotal + $finalTotal;
            $gradeRow = GradeSystem::where('from', '<=', $overallStudentScore)
                ->where('to', '>=', $overallStudentScore)
                ->first();

            if (!$hasFailed) {
                $totalStudentsPassed++;
            }
            $totalOverallScore += $overallStudentScore;

            // --- Fetch Attendance Data (Placeholder - Replace with your actual logic) ---
            // You'll need to implement logic to fetch actual attendance data for the student
            // based on exam terms (mid_term, final).
            $attendance = [
                'mid_term' => ['present' => rand(80, 100), 'absent' => rand(0, 5), 'leave' => rand(0, 2), 'sick' => rand(0, 1)],
                'final'    => ['present' => rand(80, 100), 'absent' => rand(0, 5), 'leave' => rand(0, 2), 'sick' => rand(0, 1)],
            ];

            // Add student's subject rows
            $firstSubjectRowForStudent = true;
            foreach ($studentSubjectRows as $row) {
                $rowData = [
                    'student_id'   => $firstSubjectRowForStudent ? $student->id : '',
                    'student_name' => $firstSubjectRowForStudent ? $student->name : '',
                    'father_name' => $firstSubjectRowForStudent ? ($student->father_name ?? '') : '',
                    'grandfather_name' => $firstSubjectRowForStudent ? ($student->grandfather_name ?? '') : '',
                    'subject'      => $row['subject'],
                    'mid_term'     => $row['mid_term'],
                    'final'        => $row['final'],
                    'total'        => $row['total'],
                    'written_marks' => $row['written_marks'],
                    'recital_marks' => $row['recital_marks'],
                    'homework_marks' => $row['homework_marks'],
                    'class_activity_marks' => $row['class_activity_marks'],
                    'mark_in_words' => $row['mark_in_words'],
                    // Attendance data only on the first subject row for visual "merging"
                    'attendance_mid_present' => $firstSubjectRowForStudent ? $attendance['mid_term']['present'] : '',
                    'attendance_mid_absent'  => $firstSubjectRowForStudent ? $attendance['mid_term']['absent'] : '',
                    'attendance_mid_leave'   => $firstSubjectRowForStudent ? $attendance['mid_term']['leave'] : '',
                    'attendance_mid_sick'    => $firstSubjectRowForStudent ? $attendance['mid_term']['sick'] : '',
                    'attendance_final_present' => $firstSubjectRowForStudent ? $attendance['final']['present'] : '',
                    'attendance_final_absent'  => $firstSubjectRowForStudent ? $attendance['final']['absent'] : '',
                    'attendance_final_leave'   => $firstSubjectRowForStudent ? $attendance['final']['leave'] : '',
                    'attendance_final_sick'    => $firstSubjectRowForStudent ? $attendance['final']['sick'] : '',
                    'student_code' => $firstSubjectRowForStudent ? ($student->student->student_code ?? '---') : '',
                ];
                $data[] = $rowData;
                $this->dataRows[] = ['type' => 'subject', 'student_id' => $student->id, 'row' => $currentRow];
                $firstSubjectRowForStudent = false;
                $currentRow++;
            }

            // Add student summary row
            $data[] = [
                'student_id'   => 'مجموعه نمرات: ' . $overallStudentScore,
                'student_name' => 'نتیجه: ' . ($hasFailed ? 'ناکام' : 'کامیاب'), // Fail/Pass in Persian
                'father_name' => '',
                'grandfather_name' => '',
                'subject'      => 'درجه: ' . ($gradeRow->title ?? 'نامعلوم'), // Grade in Persian
                'mid_term'     => '', 'final' => '', 'total' => '',
                'written_marks' => '', 'recital_marks' => '', 'homework_marks' => '', 'class_activity_marks' => '', 'mark_in_words' => '',
                'attendance_mid_present' => '', 'attendance_mid_absent' => '', 'attendance_mid_leave' => '', 'attendance_mid_sick' => '',
                'attendance_final_present' => '', 'attendance_final_absent' => '', 'attendance_final_leave' => '', 'attendance_final_sick' => '',
                'student_code' => '',
            ];
            $this->dataRows[] = ['type' => 'student_summary', 'student_id' => $student->id, 'row' => $currentRow];
            $this->studentSummaryRows[] = $currentRow;
            $currentRow++;

            // Separator row
            $data[] = $this->createRow('', 'separator', $currentRow++);
        }

        // Add overall class summary row
        $averageOverallScore = $students->count() > 0 ? round($totalOverallScore / $students->count(), 2) : 0;
        $passRate = $students->count() > 0 ? round(($totalStudentsPassed / $students->count()) * 100, 2) : 0;

        $data[] = [
            'student_id'   => 'خلاصه کلی صنف',
            'student_name' => 'تعداد شاگردان: ' . $students->count(),
            'father_name' => '',
            'grandfather_name' => '',
            'subject'      => 'میانگین نمرات: ' . $averageOverallScore,
            'mid_term'     => 'درصد قبولی: ' . $passRate . '%',
            'final'        => '', 'total' => '',
            'written_marks' => '', 'recital_marks' => '', 'homework_marks' => '', 'class_activity_marks' => '', 'mark_in_words' => '',
            'attendance_mid_present' => '', 'attendance_mid_absent' => '', 'attendance_mid_leave' => '', 'attendance_mid_sick' => '',
            'attendance_final_present' => '', 'attendance_final_absent' => '', 'attendance_final_leave' => '', 'attendance_final_sick' => '',
            'student_code' => '',
        ];
        $this->dataRows[] = ['type' => 'overall_summary', 'row' => $currentRow];
        $this->overallSummaryRow = $currentRow;
        $currentRow++;

        return collect($data);
    }

    // Helper to create a row with all defined keys, setting the first column and type
    protected function createRow(string $firstColValue, string $type, int $rowNum): array
    {
        $newRow = array_fill_keys(self::COLUMNS, '');
        $newRow['student_id'] = $firstColValue;
        $this->dataRows[] = ['type' => $type, 'row' => $rowNum];
        return $newRow;
    }

    public function headings(): array
    {
        // Headings will appear after the initial rows added in collection()
        return [
            'شماره شاگرد', // Student ID
            'نام شاگرد', // Student Name
            'نام پدر', // Father Name
            'نام پدر کلان', // Grandfather Name
            'مضامین', // Subjects
            'نمره 4/5 ماهه', // Mid Term Marks
            'نمره سالانه', // Final Marks
            'مجموعه نمرات', // Total Marks
            'نمره تحریری', // Written Marks
            'نمره تقریری', // Recital Marks
            'نمره کارخانگی', // Homework Marks
            'نمره فعالیت صنفی', // Class Activity Marks
            'نمره به حروف', // Mark in Words
            'حاضر (4/5 ماهه)', // Present (Mid Term)
            'غیر حاضر (4/5 ماهه)', // Absent (Mid Term)
            'رخصت (4/5 ماهه)', // Leave (Mid Term)
            'مریض (4/5 ماهه)', // Sick (Mid Term)
            'حاضر (سالانه)', // Present (Annual)
            'غیر حاضر (سالانه)', // Absent (Annual)
            'رخصت (سالانه)', // Leave (Annual)
            'مریض (سالانه)', // Sick (Annual)
            'نمبر اساس', // Student Code
        ];
    }

    public function map($row): array
    {
        // All rows passed to map will be associative, so we can use keys
        return [
            $row['student_id'],
            $row['student_name'],
            $row['father_name'],
            $row['grandfather_name'],
            $row['subject'],
            $row['mid_term'],
            $row['final'],
            $row['total'],
            $row['written_marks'],
            $row['recital_marks'],
            $row['homework_marks'],
            $row['class_activity_marks'],
            $row['mark_in_words'],
            $row['attendance_mid_present'],
            $row['attendance_mid_absent'],
            $row['attendance_mid_leave'],
            $row['attendance_mid_sick'],
            $row['attendance_final_present'],
            $row['attendance_final_absent'],
            $row['attendance_final_leave'],
            $row['attendance_final_sick'],
            $row['student_code'],
        ];
    }

    public function title(): string
    {
        return $this->className . ' Results';
    }

    public function styles(Worksheet $sheet)
    {
        // Define common colors for consistency
        $headerBgColor = 'FF2F5496'; // Dark Blue
        $headerTextColor = 'FFFFFFFF'; // White
        $titleBgColor = 'FFD9E1F2'; // Light Blue-Grey
        $summaryBgColor = 'FFE2EFDA'; // Light Greenish-Grey
        $overallSummaryBgColor = 'FFC6EFCE'; // Light Green
        $borderColor = 'FFBFBFBF'; // Medium Grey
        $failColor = 'FFFFC7CE'; // Light Red
        $passColor = 'FFC6EFCE'; // Light Green

        // Determine the last column letter dynamically
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count(self::COLUMNS));

        // --- Report Header Styling ---
        $reportTitleRow = 1;
        $ministryTitleRow = 2;
        $directorateTitleRow = 3;
        $schoolNameRow = 4;
        $reportTitleFaRow = 5;
        $generatedDateRow = 6;
        $spacingBeforeHeadingsRow = 7;
        $headerRow = 8; // Headings are inserted after 7 rows from collection()

        // Government Title
        $sheet->getStyle('A' . $reportTitleRow . ':' . $lastCol . $reportTitleRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'color' => ['argb' => 'FF2F5496']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $titleBgColor]],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => $borderColor]]],
        ]);
        $sheet->getRowDimension($reportTitleRow)->setRowHeight(30);

        // Ministry, Directorate, School Name
        $sheet->getStyle('A' . $ministryTitleRow . ':' . $lastCol . $schoolNameRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF333333']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $titleBgColor]],
        ]);

        // Report Title (Persian)
        $sheet->getStyle('A' . $reportTitleFaRow . ':' . $lastCol . $reportTitleFaRow)->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF333333']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $titleBgColor]],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => $borderColor]]],
        ]);
        $sheet->getRowDimension($reportTitleFaRow)->setRowHeight(25);

        // Date Generated
        $sheet->getStyle('A' . $generatedDateRow . ':' . $lastCol . $generatedDateRow)->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF666666']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => $borderColor]]],
        ]);

        // --- Headings Styling ---
        $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => $headerTextColor]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBgColor]],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => $borderColor]],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Apply borders to all data cells (starting from the first actual data row after headings)
        $sheet->getStyle('A' . ($headerRow + 1) . ':' . $lastCol . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => $borderColor]],
            ],
        ]);

        // Align numeric columns to the right for better readability
        // Mid Term, Final, Total, and all Attendance columns
        $sheet->getStyle('D' . ($headerRow + 1) . ':' . $lastCol . $sheet->getHighestRow())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // --- Apply styles to student summary rows ---
        foreach ($this->studentSummaryRows as $rowNum) {
            $sheet->getStyle('A' . $rowNum . ':' . $lastCol . $rowNum)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => 'FF333333']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $summaryBgColor]],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => $borderColor]],
                    'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => $borderColor]],
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => $borderColor]],
                ],
            ]);
            // Align summary text to left
            $sheet->getStyle('A' . $rowNum . ':C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            // Align numerical parts of summary to right (if applicable, e.g., total score)
            $sheet->getStyle('D' . $rowNum . ':' . $lastCol . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // --- Apply styles to overall class summary row ---
        if ($this->overallSummaryRow > 0) {
            $sheet->getStyle('A' . $this->overallSummaryRow . ':' . $lastCol . $this->overallSummaryRow)->applyFromArray([
                'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $overallSummaryBgColor]],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => 'FF000000']],
                    'bottom' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => 'FF000000']],
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => $borderColor]],
                ],
            ]);
            // Align overall summary text
            $sheet->getStyle('A' . $this->overallSummaryRow . ':C' . $this->overallSummaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('D' . $this->overallSummaryRow . ':' . $lastCol . $this->overallSummaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        // --- Conditional formatting for Pass/Fail on Total Marks ---
        foreach ($this->dataRows as $dataRow) {
            if ($dataRow['type'] === 'subject') {
                $totalCell = 'F' . $dataRow['row']; // 'F' is the 'Total' column
                $totalValue = $sheet->getCell($totalCell)->getValue();
                if (is_numeric($totalValue)) {
                    if ($totalValue < 80) { // Example threshold for "Fail"
                        $sheet->getStyle($totalCell)->applyFromArray([
                            'font' => ['color' => ['argb' => 'FF9C0006']], // Dark red text
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $failColor]],
                        ]);
                    } else { // Example threshold for "Pass"
                        $sheet->getStyle($totalCell)->applyFromArray([
                            'font' => ['color' => ['argb' => 'FF006100']], // Dark green text
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $passColor]],
                        ]);
                    }
                }
            }
        }

        // Set column widths to auto-size
        for ($i = 1; $i <= count(self::COLUMNS); $i++) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }
}


// namespace App\Exports;

// use App\Models\User;
// use App\Models\ExamResult;
// use App\Models\GradeSystem;
// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\WithMapping;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use Maatwebsite\Excel\Concerns\WithTitle;
// use Maatwebsite\Excel\Concerns\WithStyles;
// use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
// use PhpOffice\PhpSpreadsheet\Style\Border;
// use PhpOffice\PhpSpreadsheet\Style\Fill;
// use PhpOffice\PhpSpreadsheet\Style\Alignment;
// use Illuminate\Support\Collection;
// use Carbon\Carbon;

// class ClassResultsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
// {
//     protected $classId;
//     protected $className;
//     protected $dataRows = []; // To store processed data for styling
//     protected $studentSummaryRows = []; // To store row numbers for student summaries
//     protected $overallSummaryRow = 0; // To store row number for overall summary

//     // Define all column keys for consistency across all rows
//     const COLUMNS = [
//         'student_id', 'student_name', 'subject', 'mid_term', 'final', 'total',
//         'attendance_mid_present', 'attendance_mid_absent', 'attendance_mid_leave', 'attendance_mid_sick',
//         'attendance_final_present', 'attendance_final_absent', 'attendance_final_leave', 'attendance_final_sick',
//         'student_code'
//     ];

//     public function __construct($classId, $className)
//     {
//         $this->classId = $classId;
//         $this->className = $className;
//     }

//     public function collection(): Collection
//     {
//         $students = User::whereHas('studentClasses', function ($query) {
//             $query->where('class_id', $this->classId)
//                   ->where('status', 'active');
//         })->get();

//         $data = [];
//         $currentRow = 1; // Start row for actual data

//         // --- Report Header (mimicking HTML structure) ---
//         $data[] = $this->createRow('دولت جمهوری اسلامی افغانستان', 'gov_title', $currentRow++);
//         $data[] = $this->createRow('وزارت معـــــــــارف', 'ministry_title', $currentRow++);
//         $data[] = $this->createRow('ریاست معارف شهر', 'directorate_title', $currentRow++);
//         $data[] = $this->createRow(env('SCHOOL_NAME_FA', 'نام مکتب'), 'school_name', $currentRow++); // Use env for school name
//         $data[] = $this->createRow('کارنامه تعلیمی - سال تعلیمی: ' . (Carbon::now()->year - 621), 'report_title_fa', $currentRow++); // Persian year
//         $data[] = $this->createRow('تاریخ تهیه: ' . Carbon::now()->format('Y-m-d H:i:s'), 'generated_date', $currentRow++);
//         $data[] = $this->createRow('', 'spacing_before_headings', $currentRow++); // Empty row for spacing

//         // --- Student Data ---
//         $totalStudentsPassed = 0;
//         $totalOverallScore = 0;

//         foreach ($students as $student) {
//             $results = ExamResult::with(['exam', 'subject'])
//                 ->where('student_id', $student->id)
//                 ->where('class_id', $this->classId)
//                 ->get()
//                 ->groupBy(fn($r) => $r->subject->name ?? '-');

//             $midTotal = 0;
//             $finalTotal = 0;
//             $studentSubjectRows = [];

//             foreach ($results as $subject => $exams) {
//                 $mid = $exams->where('exam.exam_type', 'mid_term')->first()?->marks ?? 0;
//                 $final = $exams->where('exam.exam_type', 'final')->first()?->marks ?? 0;
//                 $studentSubjectRows[] = [
//                     'student_id'   => $student->id,
//                     'student_name' => $student->name,
//                     'subject'      => $subject,
//                     'mid_term'     => $mid,
//                     'final'        => $final,
//                     'total'        => $mid + $final,
//                 ];
//                 $midTotal += $mid;
//                 $finalTotal += $final;
//             }

//             $hasFailed = collect($studentSubjectRows)->contains(fn($row) => $row['mid_term'] < 40 || $row['final'] < 40);
//             $overallStudentScore = $midTotal + $finalTotal;
//             $gradeRow = GradeSystem::where('from', '<=', $overallStudentScore)
//                 ->where('to', '>=', $overallStudentScore)
//                 ->first();

//             if (!$hasFailed) {
//                 $totalStudentsPassed++;
//             }
//             $totalOverallScore += $overallStudentScore;

//             // --- Fetch Attendance Data (Placeholder - Replace with your actual logic) ---
//             // You'll need to implement logic to fetch actual attendance data for the student
//             // based on exam terms (mid_term, final).
//             $attendance = [
//                 'mid_term' => ['present' => rand(80, 100), 'absent' => rand(0, 5), 'leave' => rand(0, 2), 'sick' => rand(0, 1)],
//                 'final'    => ['present' => rand(80, 100), 'absent' => rand(0, 5), 'leave' => rand(0, 2), 'sick' => rand(0, 1)],
//             ];

//             // Add student's subject rows
//             $firstSubjectRowForStudent = true;
//             foreach ($studentSubjectRows as $row) {
//                 $rowData = [
//                     'student_id'   => $firstSubjectRowForStudent ? $student->id : '',
//                     'student_name' => $firstSubjectRowForStudent ? $student->name : '',
//                     'subject'      => $row['subject'],
//                     'mid_term'     => $row['mid_term'],
//                     'final'        => $row['final'],
//                     'total'        => $row['total'],
//                     // Attendance data only on the first subject row for visual "merging"
//                     'attendance_mid_present' => $firstSubjectRowForStudent ? $attendance['mid_term']['present'] : '',
//                     'attendance_mid_absent'  => $firstSubjectRowForStudent ? $attendance['mid_term']['absent'] : '',
//                     'attendance_mid_leave'   => $firstSubjectRowForStudent ? $attendance['mid_term']['leave'] : '',
//                     'attendance_mid_sick'    => $firstSubjectRowForStudent ? $attendance['mid_term']['sick'] : '',
//                     'attendance_final_present' => $firstSubjectRowForStudent ? $attendance['final']['present'] : '',
//                     'attendance_final_absent'  => $firstSubjectRowForStudent ? $attendance['final']['absent'] : '',
//                     'attendance_final_leave'   => $firstSubjectRowForStudent ? $attendance['final']['leave'] : '',
//                     'attendance_final_sick'    => $firstSubjectRowForStudent ? $attendance['final']['sick'] : '',
//                     'student_code' => $firstSubjectRowForStudent ? ($student->student->student_code ?? '---') : '',
//                 ];
//                 $data[] = $rowData;
//                 $this->dataRows[] = ['type' => 'subject', 'student_id' => $student->id, 'row' => $currentRow];
//                 $firstSubjectRowForStudent = false;
//                 $currentRow++;
//             }

//             // Add student summary row
//             $data[] = [
//                 'student_id'   => 'مجموعه نمرات: ' . $overallStudentScore,
//                 'student_name' => 'نتیجه: ' . ($hasFailed ? 'ناکام' : 'کامیاب'), // Fail/Pass in Persian
//                 'subject'      => 'درجه: ' . ($gradeRow->title ?? 'نامعلوم'), // Grade in Persian
//                 'mid_term'     => '', 'final' => '', 'total' => '',
//                 'attendance_mid_present' => '', 'attendance_mid_absent' => '', 'attendance_mid_leave' => '', 'attendance_mid_sick' => '',
//                 'attendance_final_present' => '', 'attendance_final_absent' => '', 'attendance_final_leave' => '', 'attendance_final_sick' => '',
//                 'student_code' => '',
//             ];
//             $this->dataRows[] = ['type' => 'student_summary', 'student_id' => $student->id, 'row' => $currentRow];
//             $this->studentSummaryRows[] = $currentRow;
//             $currentRow++;

//             // Separator row
//             $data[] = $this->createRow('', 'separator', $currentRow++);
//         }

//         // Add overall class summary row
//         $averageOverallScore = $students->count() > 0 ? round($totalOverallScore / $students->count(), 2) : 0;
//         $passRate = $students->count() > 0 ? round(($totalStudentsPassed / $students->count()) * 100, 2) : 0;

//         $data[] = [
//             'student_id'   => 'خلاصه کلی صنف',
//             'student_name' => 'تعداد شاگردان: ' . $students->count(),
//             'subject'      => 'میانگین نمرات: ' . $averageOverallScore,
//             'mid_term'     => 'درصد قبولی: ' . $passRate . '%',
//             'final'        => '', 'total' => '',
//             'attendance_mid_present' => '', 'attendance_mid_absent' => '', 'attendance_mid_leave' => '', 'attendance_mid_sick' => '',
//             'attendance_final_present' => '', 'attendance_final_absent' => '', 'attendance_final_leave' => '', 'attendance_final_sick' => '',
//             'student_code' => '',
//         ];
//         $this->dataRows[] = ['type' => 'overall_summary', 'row' => $currentRow];
//         $this->overallSummaryRow = $currentRow;
//         $currentRow++;

//         return collect($data);
//     }

//     // Helper to create a row with all defined keys, setting the first column and type
//     protected function createRow(string $firstColValue, string $type, int $rowNum): array
//     {
//         $newRow = array_fill_keys(self::COLUMNS, '');
//         $newRow['student_id'] = $firstColValue;
//         $this->dataRows[] = ['type' => $type, 'row' => $rowNum];
//         return $newRow;
//     }

//     public function headings(): array
//     {
//         // Headings will appear after the initial rows added in collection()
//         return [
//             'شماره شاگرد', // Student ID
//             'نام شاگرد', // Student Name
//             'مضامین', // Subjects
//             'نمره 4/5 ماهه', // Mid Term Marks
//             'نمره سالانه', // Final Marks
//             'مجموعه نمرات', // Total Marks
//             'حاضر (4/5 ماهه)', // Present (Mid Term)
//             'غیر حاضر (4/5 ماهه)', // Absent (Mid Term)
//             'رخصت (4/5 ماهه)', // Leave (Mid Term)
//             'مریض (4/5 ماهه)', // Sick (Mid Term)
//             'حاضر (سالانه)', // Present (Annual)
//             'غیر حاضر (سالانه)', // Absent (Annual)
//             'رخصت (سالانه)', // Leave (Annual)
//             'مریض (سالانه)', // Sick (Annual)
//             'نمبر اساس', // Student Code
//         ];
//     }

//     public function map($row): array
//     {
//         // All rows passed to map will be associative, so we can use keys
//         return [
//             $row['student_id'],
//             $row['student_name'],
//             $row['subject'],
//             $row['mid_term'],
//             $row['final'],
//             $row['total'],
//             $row['attendance_mid_present'],
//             $row['attendance_mid_absent'],
//             $row['attendance_mid_leave'],
//             $row['attendance_mid_sick'],
//             $row['attendance_final_present'],
//             $row['attendance_final_absent'],
//             $row['attendance_final_leave'],
//             $row['attendance_final_sick'],
//             $row['student_code'],
//         ];
//     }

//     public function title(): string
//     {
//         return $this->className . ' Results';
//     }

//     public function styles(Worksheet $sheet)
//     {
//         // Define common colors for consistency
//         $headerBgColor = 'FF2F5496'; // Dark Blue
//         $headerTextColor = 'FFFFFFFF'; // White
//         $titleBgColor = 'FFD9E1F2'; // Light Blue-Grey
//         $summaryBgColor = 'FFE2EFDA'; // Light Greenish-Grey
//         $overallSummaryBgColor = 'FFC6EFCE'; // Light Green
//         $borderColor = 'FFBFBFBF'; // Medium Grey
//         $failColor = 'FFFFC7CE'; // Light Red
//         $passColor = 'FFC6EFCE'; // Light Green

//         // Determine the last column letter dynamically
//         $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count(self::COLUMNS));

//         // --- Report Header Styling ---
//         $reportTitleRow = 1;
//         $ministryTitleRow = 2;
//         $directorateTitleRow = 3;
//         $schoolNameRow = 4;
//         $reportTitleFaRow = 5;
//         $generatedDateRow = 6;
//         $spacingBeforeHeadingsRow = 7;
//         $headerRow = 8; // Headings are inserted after 7 rows from collection()

//         // Government Title
//         $sheet->getStyle('A' . $reportTitleRow . ':' . $lastCol . $reportTitleRow)->applyFromArray([
//             'font' => ['bold' => true, 'size' => 18, 'color' => ['argb' => 'FF2F5496']],
//             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
//             'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $titleBgColor]],
//             'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => $borderColor]]],
//         ]);
//         $sheet->getRowDimension($reportTitleRow)->setRowHeight(30);

//         // Ministry, Directorate, School Name
//         $sheet->getStyle('A' . $ministryTitleRow . ':' . $lastCol . $schoolNameRow)->applyFromArray([
//             'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF333333']],
//             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
//             'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $titleBgColor]],
//         ]);

//         // Report Title (Persian)
//         $sheet->getStyle('A' . $reportTitleFaRow . ':' . $lastCol . $reportTitleFaRow)->applyFromArray([
//             'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF333333']],
//             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
//             'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $titleBgColor]],
//             'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => $borderColor]]],
//         ]);
//         $sheet->getRowDimension($reportTitleFaRow)->setRowHeight(25);

//         // Date Generated
//         $sheet->getStyle('A' . $generatedDateRow . ':' . $lastCol . $generatedDateRow)->applyFromArray([
//             'font' => ['italic' => true, 'size' => 10, 'color' => ['argb' => 'FF666666']],
//             'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
//             'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => $borderColor]]],
//         ]);

//         // --- Headings Styling ---
//         $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $headerRow)->applyFromArray([
//             'font' => ['bold' => true, 'color' => ['argb' => $headerTextColor]],
//             'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $headerBgColor]],
//             'borders' => [
//                 'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => $borderColor]],
//             ],
//             'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
//         ]);

//         // Apply borders to all data cells (starting from the first actual data row after headings)
//         $sheet->getStyle('A' . ($headerRow + 1) . ':' . $lastCol . $sheet->getHighestRow())->applyFromArray([
//             'borders' => [
//                 'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => $borderColor]],
//             ],
//         ]);

//         // Align numeric columns to the right for better readability
//         // Mid Term, Final, Total, and all Attendance columns
//         $sheet->getStyle('D' . ($headerRow + 1) . ':' . $lastCol . $sheet->getHighestRow())->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

//         // --- Apply styles to student summary rows ---
//         foreach ($this->studentSummaryRows as $rowNum) {
//             $sheet->getStyle('A' . $rowNum . ':' . $lastCol . $rowNum)->applyFromArray([
//                 'font' => ['bold' => true, 'color' => ['argb' => 'FF333333']],
//                 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $summaryBgColor]],
//                 'borders' => [
//                     'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => $borderColor]],
//                     'bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['argb' => $borderColor]],
//                     'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => $borderColor]],
//                 ],
//             ]);
//             // Align summary text to left
//             $sheet->getStyle('A' . $rowNum . ':C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
//             // Align numerical parts of summary to right (if applicable, e.g., total score)
//             $sheet->getStyle('D' . $rowNum . ':' . $lastCol . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
//         }

//         // --- Apply styles to overall class summary row ---
//         if ($this->overallSummaryRow > 0) {
//             $sheet->getStyle('A' . $this->overallSummaryRow . ':' . $lastCol . $this->overallSummaryRow)->applyFromArray([
//                 'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF000000']],
//                 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $overallSummaryBgColor]],
//                 'borders' => [
//                     'top' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => 'FF000000']],
//                     'bottom' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => 'FF000000']],
//                     'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => $borderColor]],
//                 ],
//             ]);
//             // Align overall summary text
//             $sheet->getStyle('A' . $this->overallSummaryRow . ':C' . $this->overallSummaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
//             $sheet->getStyle('D' . $this->overallSummaryRow . ':' . $lastCol . $this->overallSummaryRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
//         }

//         // --- Conditional formatting for Pass/Fail on Total Marks ---
//         foreach ($this->dataRows as $dataRow) {
//             if ($dataRow['type'] === 'subject') {
//                 $totalCell = 'F' . $dataRow['row']; // 'F' is the 'Total' column
//                 $totalValue = $sheet->getCell($totalCell)->getValue();
//                 if (is_numeric($totalValue)) {
//                     if ($totalValue < 80) { // Example threshold for "Fail"
//                         $sheet->getStyle($totalCell)->applyFromArray([
//                             'font' => ['color' => ['argb' => 'FF9C0006']], // Dark red text
//                             'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $failColor]],
//                         ]);
//                     } else { // Example threshold for "Pass"
//                         $sheet->getStyle($totalCell)->applyFromArray([
//                             'font' => ['color' => ['argb' => 'FF006100']], // Dark green text
//                             'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $passColor]],
//                         ]);
//                     }
//                 }
//             }
//         }

//         // Set column widths to auto-size
//         for ($i = 1; $i <= count(self::COLUMNS); $i++) {
//             $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
//         }
//     }
// }
