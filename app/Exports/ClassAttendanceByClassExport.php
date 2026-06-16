<?php

namespace App\Exports;

use App\Models\ClassAttendance;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// composer require morilog/jalali
use Morilog\Jalali\Jalalian;

class ClassAttendanceByClassExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnFormatting,
    ShouldAutoSize,
    WithEvents,
    WithTitle
{
    use Exportable;

    public function __construct(
        public int $classId,
        public ?string $from = null,   // YYYY-MM-DD (Gregorian)
        public ?string $until = null,  // YYYY-MM-DD (Gregorian)
    ) {}

    /* ---------------- Data ---------------- */

    public function query()
    {
        return ClassAttendance::query()
            ->with(['student', 'schoolClass.branch'])
            ->where('class_id', $this->classId)
            ->when($this->from, fn($q) => $q->whereDate('date', '>=', $this->from))
            ->when($this->until, fn($q) => $q->whereDate('date', '<=', $this->until))
            ->orderBy('student_id')
            ->orderBy('date');
    }

    public function title(): string
    {
        return 'د حاضری راپور';
    }

    /* ---------------- Headings (Pashto + RTL + Shamsi + no Fridays) ---------------- */

    public function headings(): array
    {
        $days = $this->getGregorianWorkDays(); // (no Fridays)
        $dayLabels = array_map(fn($gDate) => $this->pashtoWeekdayLabel($gDate), $days);

        $periodText = 'موده: ' . $this->rangeJalaliText();

        $head = [
            ['د زده کوونکو د حاضری راپور'],
            ['ښوونځی: ' . env('APP_NAME', 'د ښوونځي مدیریت سیستم')],
            ['ټولګی: ' . $this->getClassName()],
            [$periodText],
            ['کلید: ✓ = حاضر، ✗ = غیرحاضر، 🏥 = ناروغ، 🏠 = رخصتی، - = ثبت نه دی'],
            [''], // spacing
        ];

        // Row 7 (weekday + day), Row 8 (سهار/ماسپښین)
        $row7 = ['شمېره', 'د زده کوونکي نوم', 'د پلار نوم'];
        foreach ($dayLabels as $label) {
            $row7[] = $label;  // merged over two columns
            $row7[] = '';
        }
        $row7[] = 'ټول حاضري';
        $row7[] = 'ټول غیرحاضري';
        $row7[] = 'ټول ناروغ';
        $row7[] = 'ټول رخصتي';
        $row7[] = 'د حاضري سلنه';

        $row8 = ['', '', ''];
        foreach ($days as $_) {
            $row8[] = 'سهار';
            $row8[] = 'ماسپښین';
        }
        $row8[] = '';
        $row8[] = '';
        $row8[] = '';
        $row8[] = '';
        $row8[] = '';

        $head[] = $row7;
        $head[] = $row8;

        return $head;
    }

    public function map($row): array
    {
        return [];
    } // fill in AfterSheet

    /* ---------------- Styles ---------------- */

    public function styles(Worksheet $sheet)
    {
        $daysCount = count($this->getGregorianWorkDays());
        // 3 info + (days*2) + present + absent + percentage => +3 (was +2)
        $lastColIdx = 3 + ($daysCount * 2) + 5;
        $lastCol = $this->col($lastColIdx);



        $sheet->setRightToLeft(true);

        // Title
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(18);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_DARKBLUE);

        // School
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Class
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Period
        $sheet->mergeCells("A4:{$lastCol}4");
        $sheet->getStyle('A4')->getFont()->setItalic(true);
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Legend
        $sheet->mergeCells("A5:{$lastCol}5");
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5')->getFont()->setSize(10);

        // Row 7 (weekday+day)
        $sheet->getStyle("A7:{$lastCol}7")->getFont()->setBold(true);
        $sheet->getStyle("A7:{$lastCol}7")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A7:{$lastCol}7")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFE8F1FF');
        $sheet->getStyle("A7:{$lastCol}7")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

        // Row 8 (سهار/ماسپښین)
        $sheet->getStyle("A8:{$lastCol}8")->getFont()->setBold(true);
        $sheet->getStyle("A8:{$lastCol}8")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A8:{$lastCol}8")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF0F5FF');
        $sheet->getStyle("A8:{$lastCol}8")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

        // Merge each date header across two columns
        $col = 4; // start at D
        foreach ($this->getGregorianWorkDays() as $_) {
            $start = $this->col($col);
            $end   = $this->col($col + 1);
            $sheet->mergeCells("{$start}7:{$end}7");
            $col += 2;
        }

        // Freeze after subheaders
        $sheet->freezePane('D9');

        // Set print settings
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.5);
        $sheet->getPageMargins()->setRight(0.25);
        $sheet->getPageMargins()->setLeft(0.25);
        $sheet->getPageMargins()->setBottom(0.5);

        return [];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Serial number
            'B' => NumberFormat::FORMAT_TEXT, // Student name
            'C' => NumberFormat::FORMAT_TEXT, // Father name
        ];
    }

    /* ---------------- Body & final formatting ---------------- */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $e) {
                $sheet = $e->sheet->getDelegate();

                $days = $this->getGregorianWorkDays();
                $daysCount = count($days);
                $totalSessions = $daysCount * 2;

                // 3 info + (days*2) + present + absent + percentage
                $lastColIdx = 3 + ($daysCount * 2) + 5;
                $lastCol = $this->col($lastColIdx);

                $students = $this->compileMarks($days);

                $row = 9;
                $serial = 1;
                foreach ($students as $s) {
                    $c = 1;
                    $sheet->setCellValue($this->cell($c++, $row), $this->toPashtoDigits((string)$serial));
                    $sheet->setCellValue($this->cell($c++, $row), $s['name']);
                    $sheet->setCellValue($this->cell($c++, $row), $s['father']);

                    foreach ($days as $d) {
                        $mor = $s['marks'][$d]['morning'] ?? '-';
                        $aft = $s['marks'][$d]['afternoon'] ?? '-';

                        $sheet->setCellValue($this->cell($c, $row), $mor);
                        $this->styleAttendanceCell($sheet, $this->cell($c, $row), $mor);
                        $c++;

                        $sheet->setCellValue($this->cell($c, $row), $aft);
                        $this->styleAttendanceCell($sheet, $this->cell($c, $row), $aft);
                        $c++;
                    }

                    // Total present
                    $sheet->setCellValue($this->cell($c, $row), $this->toPashtoDigits((string)$s['present_total']));
                    $sheet->getStyle($this->cell($c, $row))
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle($this->cell($c, $row))->getFont()->setBold(true);
                    $c++;

                    // Total absent (NEW)
                    $sheet->setCellValue($this->cell($c, $row), $this->toPashtoDigits((string)$s['absent_total']));
                    $sheet->getStyle($this->cell($c, $row))
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle($this->cell($c, $row))->getFont()->setBold(true)
                        ->getColor()->setARGB('FF8B0000'); // dark red
                    $c++;

                    // Total sick
                    $sheet->setCellValue($this->cell($c, $row), $this->toPashtoDigits((string)$s['sick_total']));
                    $sheet->getStyle($this->cell($c, $row))
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle($this->cell($c, $row))->getFont()->setBold(true)
                        ->getColor()->setARGB('FF8B4513'); // brown
                    $c++;

                    // Total leave
                    $sheet->setCellValue($this->cell($c, $row), $this->toPashtoDigits((string)$s['leave_total']));
                    $sheet->getStyle($this->cell($c, $row))
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle($this->cell($c, $row))->getFont()->setBold(true)
                        ->getColor()->setARGB('FF4169E1'); // blue
                    $c++;

                    // Percentage (based on all sessions in range)
                    $percentage = $totalSessions > 0 ? round(($s['present_total'] / $totalSessions) * 100, 1) : 0;
                    $sheet->setCellValue($this->cell($c, $row), $this->toPashtoDigits((string)$percentage) . '%');
                    $sheet->getStyle($this->cell($c, $row))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $percentageStyle = $sheet->getStyle($this->cell($c, $row));
                    if ($percentage >= 90) {
                        $percentageStyle->getFont()->getColor()->setARGB('FF006400');
                    } elseif ($percentage >= 75) {
                        $percentageStyle->getFont()->getColor()->setARGB('FF228B22');
                    } elseif ($percentage >= 50) {
                        $percentageStyle->getFont()->getColor()->setARGB('FF8B4513');
                    } else {
                        $percentageStyle->getFont()->getColor()->setARGB('FF8B0000');
                    }

                    if ($serial % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")
                            ->getFill()->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFF8FBFF');
                    }

                    $serial++;
                    $row++;
                }

                $lastRow = $row - 1;

                $sheet->getStyle('A7:' . $lastCol . $lastRow)
                    ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getStyle('A7:' . $lastCol . $lastRow)
                    ->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM);

                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(28);
                $sheet->getColumnDimension('C')->setWidth(24);

                for ($i = 4; $i < 4 + ($daysCount * 2); $i++) {
                    $sheet->getColumnDimension($this->col($i))->setWidth(6);
                }

                // Present total column
                $sheet->getColumnDimension($this->col(4 + ($daysCount * 2)))->setWidth(12);
                // Absent total column (NEW)
                $sheet->getColumnDimension($this->col(4 + ($daysCount * 2) + 1))->setWidth(12);
                // Percentage column
                $sheet->getColumnDimension($this->col(4 + ($daysCount * 2) + 2))->setWidth(12);

                $sheet->getPageSetup()->setPrintArea('A1:' . $lastCol . $lastRow);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 8);
            },
        ];
    }


    /* ---------------- Helpers ---------------- */

    private function getClassName(): string
    {
        $class = SchoolClass::find($this->classId);
        return $class?->class_name ?? 'نامعلوم ټولګی';
    }

    /**
     * Workday list (Gregorian) between from..until, skipping FRIDAYS.
     * Returns array of Y-m-d.
     */
    private function getGregorianWorkDays(): array
    {
        $out = [];
        $start = Carbon::parse($this->from);
        $end   = Carbon::parse($this->until);
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if ($d->isFriday()) continue; // Afghanistan weekend (holiday)
            $out[] = $d->format('Y-m-d');
        }
        return $out;
    }

    /** "۱۴۰۴/۰۶/۰۱ — ۱۴۰۴/۰۶/۳۱" (Shamsi) */
    private function rangeJalaliText(): string
    {
        $a = $this->from ? Jalalian::fromCarbon(Carbon::parse($this->from))->format('Y/m/d') : '';
        $b = $this->until ? Jalalian::fromCarbon(Carbon::parse($this->until))->format('Y/m/d') : '';
        return $this->toPashtoDigits($a) . ' — ' . $this->toPashtoDigits($b);
    }

    /** Header label: "<weekday> <day>" e.g., "شنبې ۰۱" */
    private function pashtoWeekdayLabel(string $gregorianYmd): string
    {
        $c = Carbon::parse($gregorianYmd);

        // Carbon: 0=Sun,1=Mon,2=Tue,3=Wed,4=Thu,5=Fri,6=Sat
        $map = [
            6 => 'شنبې',
            0 => 'یکشنبه',
            1 => 'دوشنبه',
            2 => 'سه‌شنبه',
            3 => 'چهارشنبه',
            4 => 'پنجشنبه',
            5 => 'جمعه',
        ];
        $weekday = $map[$c->dayOfWeek] ?? '';

        $j = Jalalian::fromCarbon($c);
        $day = $this->toPashtoDigits($j->format('d'));

        return trim($weekday . ' ' . $day);
    }

    /** Convert ASCII digits to Pashto */
    private function toPashtoDigits(string $s): string
    {
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $ps = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return str_replace($en, $ps, $s);
    }

    /** 1-based column index → A.. */
    private function col(int $index): string
    {
        $letters = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letters = chr(65 + $mod) . $letters;
            $index = intdiv($index - $mod - 1, 26);
        }
        return $letters;
    }

    private function cell(int $col, int $row): string
    {
        return $this->col($col) . $row;
    }

    /**
     * Style individual attendance cells based on status
     */
    private function styleAttendanceCell($sheet, $cell, $status)
    {
        $style = $sheet->getStyle($cell);
        $style->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        switch ($status) {
            case '✓':
                $style->getFont()->getColor()->setARGB('FF006400'); // Dark green
                $style->getFont()->setBold(true);
                break;
            case '✗':
                $style->getFont()->getColor()->setARGB('FF8B0000'); // Dark red
                $style->getFont()->setBold(true);
                break;
            case '🏥':
                $style->getFont()->getColor()->setARGB('FF8B4513'); // Brown for sick
                $style->getFont()->setBold(true);
                break;
            case '🏠':
                $style->getFont()->getColor()->setARGB('FF4169E1'); // Blue for leave
                $style->getFont()->setBold(true);
                break;
            case '-':
                $style->getFont()->getColor()->setARGB('FF808080'); // Gray
                break;
        }
    }

    /**
     * Compile marks per student and date (only workdays passed in):
     * morning/afternoon: 1 → ✓, 0 → ✗, null → '-'.
     * present_total counts both sessions where ✓.
     */
    private function compileMarks(array $gDays): array
    {
        $byStudent = [];
        $records = $this->query()->get();

        foreach ($records as $r) {
            $sid = $r->student_id;
            if (!isset($byStudent[$sid])) {
                $byStudent[$sid] = [
                    'name' => $r->student?->name,
                    'father' => $r->student?->father_name,
                    'marks' => array_fill_keys($gDays, ['morning' => '-', 'afternoon' => '-']),
                    'present_total' => 0,
                    'absent_total'  => 0,
                    'sick_total' => 0,
                    'leave_total' => 0,
                ];
            }

            $date = $r->date->format('Y-m-d');
            if (!in_array($date, $gDays, true)) continue;

            $mor = $r->morning;
            $aft = $r->afternoon;

            // Handle leave and sick statuses
            if ($r->is_sick) {
                $morMark = '🏥'; // Sick symbol
                $aftMark = '🏥';
            } elseif ($r->is_leave) {
                $morMark = '🏠'; // Leave symbol
                $aftMark = '🏠';
            } else {
                $morMark = ($mor === null) ? '-' : ($mor ? '✓' : '✗');
                $aftMark = ($aft === null) ? '-' : ($aft ? '✓' : '✗');
            }

            $byStudent[$sid]['marks'][$date] = [
                'morning' => $morMark,
                'afternoon' => $aftMark
            ];
        }

        // Count totals
        foreach ($byStudent as &$s) {
            $presentDays = 0;
            $absentDays  = 0;
            $sickDays = 0;
            $leaveDays = 0;

            foreach ($s['marks'] as $pair) {
                $hasTick  = ($pair['morning'] === '✓') || ($pair['afternoon'] === '✓');
                $hasCross = ($pair['morning'] === '✗') || ($pair['afternoon'] === '✗');
                $hasSick  = ($pair['morning'] === '🏥') || ($pair['afternoon'] === '🏥');
                $hasLeave = ($pair['morning'] === '🏠') || ($pair['afternoon'] === '🏠');
                $bothDash = ($pair['morning'] === '-') && ($pair['afternoon'] === '-');

                if ($hasTick) {
                    // Any ✓ → count the whole day as present
                    $presentDays++;
                } elseif ($hasCross && !$hasTick) {
                    // No ✓ and at least one ✗ → count the day as absent
                    $absentDays++;
                } elseif ($hasSick) {
                    // Sick days are counted separately
                    $sickDays++;
                } elseif ($hasLeave) {
                    // Leave days are counted separately
                    $leaveDays++;
                } else {
                    // bothDash (no data): ignore the day in both totals
                }
            }

            $s['present_total'] = $presentDays;
            $s['absent_total']  = $absentDays;
            $s['sick_total'] = $sickDays;
            $s['leave_total'] = $leaveDays;
        }

        uasort($byStudent, fn($a, $b) => strcmp($a['name'] ?? '', $b['name'] ?? ''));

        return $byStudent;
    }
}
