<?php

namespace App\Exports;

use App\Models\TeacherPlan;
use App\Models\SchoolClass;
use Illuminate\Support\Carbon;
use Morilog\Jalali\Jalalian;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeacherPlanWordExport
{
    protected int $classId;
    protected int $year;     // Jalali year
    protected string $month; // '01'..'12' (Jalali)

    public function __construct(int $classId, int $year, string $month)
    {
        $this->classId = $classId;
        $this->year    = $year;
        $this->month   = $month;
    }

    public function export(): StreamedResponse
    {
        // Convert Jalali month to Gregorian range
        $startJ = new Jalalian($this->year, (int) $this->month, 1);
        $startG = $startJ->toCarbon()->startOfDay();
        $endG   = $startJ->addMonths()->subDays(1)->toCarbon()->endOfDay();

        // Fetch plans for class within the month (Gregorian)
        $plans = TeacherPlan::with(['subject', 'schoolClass'])
            ->where('class_id', $this->classId)
            ->whereBetween('date', [$startG->toDateString(), $endG->toDateString()])
            ->orderBy('date')
            ->get();

        $class = SchoolClass::find($this->classId);

        $monthsFa = [
            '01' => 'حمل',
            '02' => 'ثور',
            '03' => 'جوزا',
            '04' => 'سرطان',
            '05' => 'اسد',
            '06' => 'سنبله',
            '07' => 'میزان',
            '08' => 'عقرب',
            '09' => 'قوس',
            '10' => 'جدی',
            '11' => 'دلو',
            '12' => 'حوت',
        ];

        // Build Word (RTL)
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(12);
        $phpWord->setDefaultParagraphStyle(['alignment' => 'right', 'rtl' => true]);

        $section = $phpWord->addSection([
            'marginLeft' => 800,
            'marginRight' => 800,
            'marginTop' => 800,
            'marginBottom' => 800,
            'rtl' => true,
        ]);

        // Title
        $section->addText(
            'کتب ترقی تعلیم صنف ' . ($class?->class_name ?? '—') .
                ' از سال ' . $this->year .
                ' و ماه ' . ($monthsFa[$this->month] ?? $this->month),
            ['bold' => true, 'size' => 16],
            ['alignment' => 'center', 'rtl' => true]
        );
        $section->addTextBreak(1);

        // Table style
        $phpWord->addTableStyle('plansTable', [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 80,
        ], ['alignment' => 'center']);

        $table = $section->addTable('plansTable');

        // Header
        $headerP = ['alignment' => 'center', 'rtl' => true];
        $table->addRow();
        $table->addCell(2000)->addText('تاریخ', ['bold' => true], $headerP);
        $table->addCell(2500)->addText('ملاحظات', ['bold' => true], $headerP);
        $table->addCell(1800)->addText('شاگردان حاضر', ['bold' => true], $headerP);
        $table->addCell(4500)->addText('تفصیل درس تشریح شده', ['bold' => true], $headerP);
        $table->addCell(2500)->addText('مضامین', ['bold' => true], $headerP);
        $table->addCell(1000)->addText('شماره', ['bold' => true], $headerP);

        // Rows
        if ($plans->isEmpty()) {
            $table->addRow();
            $table->addCell(0, ['gridSpan' => 6])->addText(
                'هیچ ریکاردی برای این مدت یافت نشد.',
                ['italic' => true],
                ['alignment' => 'center', 'rtl' => true]
            );
        } else {
            foreach ($plans as $i => $plan) {
                $table->addRow();

                $dateFa = Jalalian::fromCarbon(Carbon::parse($plan->date))->format('Y/m/d');

                $table->addCell()->addText($dateFa, [], ['alignment' => 'center', 'rtl' => true]);
                $table->addCell()->addText($plan->details ?? '', [], ['rtl' => true]);
                $table->addCell()->addText((string) ($plan->student_count ?? 0), [], ['alignment' => 'center', 'rtl' => true]);
                $table->addCell()->addText($plan->topic_covered ?? '', [], ['rtl' => true]);
                $table->addCell()->addText($plan->subject?->name ?? '', [], ['rtl' => true]);
                $table->addCell()->addText((string) ($i + 1), [], ['alignment' => 'center', 'rtl' => true]);
            }
        }

        // Stream
        $filename = 'teacher_plans_class' . $this->classId . '_' . $this->year . '_' . $this->month . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'tplan_');
        $phpWord->save($tempFile, 'Word2007');

        return response()->streamDownload(function () use ($tempFile) {
            echo file_get_contents($tempFile);
            @unlink($tempFile);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ]);
    }

    public function download(string $filename): StreamedResponse
    {
        return $this->export();
    }
}
