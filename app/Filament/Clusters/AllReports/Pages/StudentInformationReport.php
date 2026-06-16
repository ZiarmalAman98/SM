<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Forms;
use Filament\Actions;
use Filament\Pages\Page;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Branch;
use Livewire\Attributes\Computed;

class StudentInformationReport extends Page
{
    protected static string $view = 'filament.clusters.all-reports.pages.student-information-report';

    protected static ?string $cluster = AllReports::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Student Information Report';

    protected static ?int $navigationSort = 3;

    public ?int $branch_id = null;
    public ?int $class_id = null;
    public ?string $status = 'active';
    public ?string $gender = null;
    public ?string $report_type = 'detailed';

    public function mount(): void
    {
        // Initialize default values
    }

    public function generateReport(): void
    {
        $this->validate([
            'branch_id' => 'required|exists:branches,id',
            'class_id' => 'required|exists:school_classes,id',
            'status' => 'required|in:active,inactive,all',
            'report_type' => 'required|in:detailed,summary,contact',
        ]);

        // Redirect to the report view with parameters
        $this->redirect(route('student-information-report.generate', [
            'branch_id' => $this->branch_id,
            'class_id' => $this->class_id,
            'status' => $this->status,
            'gender' => $this->gender,
            'report_type' => $this->report_type,
        ]), navigate: false);
    }

    #[Computed]
    public function classes()
    {
        if (!$this->branch_id) {
            return collect();
        }

        return SchoolClass::where('branch_id', $this->branch_id)
            ->orderBy('class_name')
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
