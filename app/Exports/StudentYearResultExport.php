<?php

namespace App\Exports;

use App\Models\ExamResult;
use App\Models\GradeSystem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Color;

// composer require morilog/jalali
use Morilog\Jalali\Jalalian;

class StudentYearResultExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    ShouldAutoSize,
    WithEvents,
    WithTitle
{
    use Exportable;

    public function __construct(
        public int $studentId,
        public int $shamsiYear // e.g. 1403
    ) {}

    private ?array $meta = null;     // header meta
    private ?array $rows = null;     // table rows
    private ?array $attendanceStats = null; // attendance statistics

    private function build(): void
    {
        if ($this->meta !== null) return;

        // Shamsi -> Gregorian range - expand to include mid-term from previous year
        $currentYearStart = (new Jalalian($this->shamsiYear, 1, 1))->toCarbon()->startOfDay();
        $currentYearEnd = (new Jalalian($this->shamsiYear + 1, 1, 1))->toCarbon()->subSecond();

        // Also include previous year for mid-term exams (academic year spans two Shamsi years)
        $previousYearStart = (new Jalalian($this->shamsiYear - 1, 1, 1))->toCarbon()->startOfDay();

        $results = ExamResult::with(['exam', 'subject', 'student', 'class'])
            ->where('student_id', $this->studentId)
            ->whereHas('exam', function($q) use ($previousYearStart, $currentYearEnd) {
                $q->whereBetween('date', [$previousYearStart, $currentYearEnd]);
            })
            ->get();

        $student   = $results->first()?->student ?: User::find($this->studentId);
        $className = $results->first()?->class?->class_name ?? 'نامعلوم ټولګی';

        // Identify exams explicitly by type to avoid relying on date ordering
        $allExams = $results->pluck('exam')->filter()->unique('id');
        $midExam   = $allExams->firstWhere('exam_type', 'mid_term');
        $finalExam = $allExams->firstWhere('exam_type', 'final');

        $midExamDate   = $midExam   ? $this->toPs(Jalalian::fromCarbon(Carbon::parse($midExam->date))->format('Y/m/d')) : '';
        $finalExamDate = $finalExam ? $this->toPs(Jalalian::fromCarbon(Carbon::parse($finalExam->date))->format('Y/m/d')) : '';

        $exam1Label = 'چهارنیمه' . ($midExamDate ? ' - ' . $midExamDate : '');
        $exam2Label = 'سالانه'   . ($finalExamDate ? ' - ' . $finalExamDate : '');

        $subjects = $results->pluck('subject')->filter()->unique('id')->sortBy('name')->values();

        $rows = [];
        $i = 1;
        $grandTotal = 0;
        $grandCount = 0;

        foreach ($subjects as $subject) {
            // Resolve per-subject marks by exam type for correctness
            $m1 = (int) optional($results->firstWhere(function ($r) use ($subject) {
                return $r->subject_id === $subject->id && ($r->exam?->exam_type === 'mid_term');
            }))->marks;

            $m2 = (int) optional($results->firstWhere(function ($r) use ($subject) {
                return $r->subject_id === $subject->id && ($r->exam?->exam_type === 'final');
            }))->marks;

            $total = $m1 + $m2;
            $avg   = $this->roundAvg($m1, $m2);
            $grade = $this->gradeTitle($total); // Use total marks for grade calculation

            $rows[] = [
                $this->toPs((string)$i),
                (string)($subject->name ?? '—'),
                $this->toPs((string)$m1),       // چهارنیمه
                $this->toPs((string)$m2),       // سالانه
                $this->toPs((string)$total),    // ټول
                $this->toPs((string)$avg),      // اوسط
                $grade,                         // درجه
                '',                             // کتنې (empty for manual remark)
            ];

            $i++;
            $grandTotal += $total;
            $grandCount += 2;
        }

        $overallAvg = $grandCount ? round($grandTotal / $grandCount, 2) : 0;
        $rows[] = [
            'مجموع',
            '',
            '',
            '',
            $this->toPs((string)$grandTotal),
            $this->toPs((string)$overallAvg),
            $this->gradeTitle($grandTotal), // Use grand total for overall grade
            '',
        ];

        $this->rows = $rows;

        // Calculate attendance statistics
        $this->attendanceStats = $this->calculateAttendanceStats($this->studentId, $this->shamsiYear);

        $this->meta = [
            'student'     => $student?->name ?? '—',
            'class'       => $className,
            'year_label'  => $this->toPs((string)$this->shamsiYear),
            'exam1_label' => $exam1Label,
            'exam2_label' => $exam2Label,
            'attendance'  => $this->attendanceStats,
        ];
    }

    public function title(): string
    {
        return 'کلنی نتیجه';
    }

    public function headings(): array
    {
        $this->build();
        $m = $this->meta;

        $headings = [
            ['قسمت کننده سه پرچه'],
            ['زده کوونکی: ' . $m['student'] . '   |   ټولګی: ' . $m['class']],
            ['تعلیمي کال: ' . $m['year_label'] . '   |   ازموینې: ' . $m['exam1_label'] . ' ، ' . $m['exam2_label']],
        ];

        // Add attendance information if available
        if (!empty($m['attendance'])) {
            $attendance = $m['attendance'];
            $attendanceText = "د حاضرۍ تفصیل: حاضر: {$this->toPs((string)$attendance['present'])} | غائب: {$this->toPs((string)$attendance['absent'])} | رخصت: {$this->toPs((string)$attendance['leave'])} | ناروغ: {$this->toPs((string)$attendance['sick'])}";
            $headings[] = [$attendanceText];
        }

        $headings[] = ['']; // spacing
        $headings[] = ['شمېره', 'مضمون', 'چهارنیمه', 'سالانه', 'ټول', 'اوسط', 'درجه', 'کتنې'];

        return $headings;
    }

    public function collection()
    {
        $this->build();
        return new Collection($this->rows ?? []);
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = 'H'; // 8 columns
        $sheet->setRightToLeft(true);

        // Page setup: A4 landscape, fit to width, print header row on each page
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(5, 5);

        // Margins (inches) - optimized for printing
        $sheet->getPageMargins()->setTop(0.3);
        $sheet->getPageMargins()->setBottom(0.3);
        $sheet->getPageMargins()->setLeft(0.2);
        $sheet->getPageMargins()->setRight(0.2);
        $sheet->getPageMargins()->setHeader(0.2);
        $sheet->getPageMargins()->setFooter(0.2);

        // Center content horizontally
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setVerticalCentered(false);

        // Merge header rows
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");

        // Check if attendance row exists (row 4)
        $hasAttendance = $sheet->getCell('A4')->getValue() !== null && $sheet->getCell('A4')->getValue() !== '';
        if ($hasAttendance) {
            $sheet->mergeCells("A4:{$lastCol}4");
            $sheet->mergeCells("A5:{$lastCol}5"); // spacing row
            $headerRow = 6; // Table headers
        } else {
            $sheet->mergeCells("A4:{$lastCol}4"); // spacing row
            $headerRow = 5; // Table headers
        }

        // Main title styling
        $sheet->getStyle("A1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
                'name' => 'Arial',
                'color' => ['rgb' => '1F497D']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DCE6F1']
            ]
        ]);

        // Subtitle styling
        $sheet->getStyle("A2:A3")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ]
        ]);

        // Header row styling
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '366092']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ]
        ]);

        // Set row heights
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);
        if ($hasAttendance) {
            $sheet->getRowDimension(4)->setRowHeight(20); // attendance row
            $sheet->getRowDimension(6)->setRowHeight(25); // header row
        } else {
            $sheet->getRowDimension(5)->setRowHeight(25); // header row
        }

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(8);  // شماره
        $sheet->getColumnDimension('B')->setWidth(30); // مضمون
        $sheet->getColumnDimension('C')->setWidth(12); // چهارنیمه
        $sheet->getColumnDimension('D')->setWidth(12); // سالانه
        $sheet->getColumnDimension('E')->setWidth(12); // ټول
        $sheet->getColumnDimension('F')->setWidth(12); // اوسط
        $sheet->getColumnDimension('G')->setWidth(12); // درجه
        $sheet->getColumnDimension('H')->setWidth(25); // کتنې

        // Freeze headers
        $freezeRow = $hasAttendance ? 7 : 6;
        $sheet->freezePane("A{$freezeRow}");

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'H';
                $lastRow = $sheet->getHighestRow();

                // Determine header row dynamically
                $hasAttendance = $sheet->getCell('A4')->getValue() !== null && $sheet->getCell('A4')->getValue() !== '';
                $headerRow = $hasAttendance ? 6 : 5;

                // Apply borders to the entire table
                $tableRange = "A{$headerRow}:{$lastCol}{$lastRow}";
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                // Center numeric columns
                $dataStartRow = $headerRow + 1;
                $sheet->getStyle("C{$dataStartRow}:G{$lastRow}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Align subject names to right
                $sheet->getStyle("B{$dataStartRow}:B" . ($lastRow - 1))->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Style the summary row
                $summaryRow = $lastRow;
                $sheet->getStyle("A{$summaryRow}:{$lastCol}{$summaryRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '5B9BD5']
                    ]
                ]);

                // Footer section for print
                $noteRow = $lastRow + 2;
                $sigRow = $lastRow + 4;
                $dateRow = $lastRow + 4;

                // Note section
                $note = 'یادونه: په پورته ډول د نوموړي زده کوونګي د ازموینې نمرې او د حاضرۍ ورځې درج شوي د صحت وړ دي.';
                $sheet->setCellValue("A{$noteRow}", $note);
                $sheet->mergeCells("A{$noteRow}:{$lastCol}{$noteRow}");
                $sheet->getStyle("A{$noteRow}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                    ]
                ]);

                // Signature section
                $sheet->setCellValue("B{$sigRow}", 'د ښوونځي آمر لاسلیک:');
                $sheet->getStyle("B{$sigRow}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                    ]
                ]);

                // Signature line
                $sheet->mergeCells("C{$sigRow}:E{$sigRow}");
                $sheet->getStyle("C{$sigRow}:E{$sigRow}")->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'],
                        ],
                    ]
                ]);

                // Date section
                $sheet->setCellValue("F{$dateRow}", 'نیټه:');
                $sheet->getStyle("F{$dateRow}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'bold' => true,
                    ]
                ]);

                $currentDate = $this->toPs(Jalalian::fromCarbon(now())->format('Y/m/d'));
                $sheet->setCellValue("G{$dateRow}", $currentDate);
                $sheet->getStyle("G{$dateRow}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '000000'],
                        ],
                    ]
                ]);

                // Set print area to include all content including footer
                $sheet->getPageSetup()->setPrintArea("A1:{$lastCol}{$dateRow}");

                // Add header and footer for printing
                $sheet->getHeaderFooter()
                    ->setOddHeader('&C&"Arial,Bold"د زده کوونکو د کلنی نتیجې راپور')
                    ->setOddFooter('&Rصفحه &P of &N');

                // Center the entire content on the page
                $sheet->getPageSetup()->setHorizontalCentered(true);
                $sheet->getPageSetup()->setVerticalCentered(false);
            },
        ];
    }

    /* ---------- Helpers ---------- */

    private function calculateAttendanceStats(int $studentId, int $shamsiYear): array
    {
        // Convert Shamsi year to Gregorian date range (academic year spans two Shamsi years)
        $currentYearStart = (new Jalalian($shamsiYear, 1, 1))->toCarbon()->startOfDay();
        $currentYearEnd = (new Jalalian($shamsiYear + 1, 1, 1))->toCarbon()->subSecond();
        $previousYearStart = (new Jalalian($shamsiYear - 1, 1, 1))->toCarbon()->startOfDay();

        // Get attendance records for the academic year
        $attendanceRecords = \App\Models\ClassAttendance::where('student_id', $studentId)
            ->whereBetween('date', [$previousYearStart, $currentYearEnd])
            ->get();

        $present = 0;
        $absent = 0;
        $leave = 0;
        $sick = 0;

        foreach ($attendanceRecords as $record) {
            if ($record->is_sick) {
                $sick++;
            } elseif ($record->is_leave) {
                $leave++;
            } elseif ($record->status === 'present') {
                $present++;
            } elseif ($record->status === 'absent') {
                $absent++;
            }
        }

        return [
            'present' => $present,
            'absent' => $absent,
            'leave' => $leave,
            'sick' => $sick,
        ];
    }

    private function roundAvg(int $m1, int $m2): float
    {
        return round(($m1 + $m2) / 2, 2);
    }

    private function gradeTitle(float $marks): string
    {
        $g = GradeSystem::where('from', '<=', $marks)->where('to', '>=', $marks)->first();
        return $g?->title ?? '';
    }

    private function toPs(string $s): string
    {
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $ps = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return str_replace($en, $ps, $s);
    }
}
