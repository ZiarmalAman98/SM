<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Pages\Page;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Exam;
use Morilog\Jalali\Jalalian;

class ExamResultsReport extends Page
{
    protected static string $view = 'filament.clusters.all-reports.pages.exam-results-report';

    protected static ?string $cluster = AllReports::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Exam Results Report';

    protected static ?int $navigationSort = 5;

    public ?int $branch_id = null;
    public ?int $class_id = null;
    public ?int $exam_id = null;
    public ?string $exam_type = 'mid_term';
    public ?string $academic_year = null;

    public function mount(): void
    {
        // Set default academic year to current Jalali year
        $this->academic_year = Jalalian::now()->getYear();
    }

    public function generateReport(): void
    {
        $this->validate([
            'branch_id' => 'required|exists:branches,id',
            'exam_type' => 'required|in:mid_term,final',
            'academic_year' => 'required|integer|min:1400|max:1450',
        ]);

        $params = [
            'branch_id' => $this->branch_id,
            'class_id' => $this->class_id,
            'exam_id' => $this->exam_id,
            'exam_type' => $this->exam_type,
            'academic_year' => $this->academic_year,
        ];

        $this->redirect(route('exam-results-report.generate', $params), navigate: false);
    }

    public function updatedBranchId($value)
    {
        // Reset class and exam when branch changes
        $this->class_id = null;
        $this->exam_id = null;
    }

    public function updatedClassId($value)
    {
        // Reset exam when class changes
        $this->exam_id = null;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
