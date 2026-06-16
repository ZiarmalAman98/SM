<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use Filament\Forms;
use Filament\Pages\Page;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Payroll;
use App\Models\Income;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class FinancialSummaryReport extends Page
{
    protected static string $view = 'filament.clusters.all-reports.pages.financial-summary-report';

    protected static ?string $cluster = AllReports::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Financial Summary Report';

    protected static ?int $navigationSort = 4;

    public ?int $branch_id = null;
    public ?int $class_id = null;
    public ?string $report_type = 'monthly';
    public ?string $start_date = null;
    public ?string $end_date = null;
    public ?string $year = null;

    public function mount(): void
    {
        // Use a completely safe approach that avoids Jalali parsing issues
        $now = Jalalian::now();
        $this->year = $now->getYear();

        // Get current month and year safely
        $currentYear = $now->getYear();
        $currentMonth = $now->getMonth();

        // Create simple Jalali date strings without using problematic methods
        $this->start_date = $currentYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-01';

        // Get the last day of the month safely
        $lastDay = $now->getMonthDays();
        $this->end_date = $currentYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($lastDay, 2, '0', STR_PAD_LEFT);
    }

    public function generateReport(): void
    {
        $this->validate([
            'branch_id' => 'required|exists:branches,id',
            'report_type' => 'required|in:monthly,quarterly,yearly,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'year' => 'nullable|integer|min:1400|max:1450',
        ]);

        // Convert Jalali dates to Gregorian for the controller
        $startDateGregorian = null;
        $endDateGregorian = null;

        if ($this->start_date && $this->end_date) {
            try {
                // Parse the Jalali date string manually and convert safely
                $startParts = explode('-', $this->start_date);
                $endParts = explode('-', $this->end_date);

                // Use the constructor with proper error handling
                $startJalali = new Jalalian((int)$startParts[0], (int)$startParts[1], (int)$startParts[2]);
                $endJalali = new Jalalian((int)$endParts[0], (int)$endParts[1], (int)$endParts[2]);

                $startDateGregorian = $startJalali->toCarbon()->format('Y-m-d');
                $endDateGregorian = $endJalali->toCarbon()->format('Y-m-d');
            } catch (\Exception $e) {
                // Fallback to current dates if parsing fails
                $startDateGregorian = now()->startOfMonth()->format('Y-m-d');
                $endDateGregorian = now()->endOfMonth()->format('Y-m-d');
            }
        }

        $params = [
            'branch_id' => $this->branch_id,
            'class_id' => $this->class_id,
            'report_type' => $this->report_type,
        ];

        // Add year parameter for yearly reports
        if ($this->report_type === 'yearly' && $this->year) {
            $params['year'] = $this->year;
        }

        // Add date parameters for custom reports
        if ($this->report_type === 'custom' && $startDateGregorian && $endDateGregorian) {
            $params['start_date'] = $startDateGregorian;
            $params['end_date'] = $endDateGregorian;
        }

        $this->redirect(route('financial-summary-report.generate', $params), navigate: false);
    }

    public function getReportUrl(): string
    {
        $params = [
            'branch_id' => $this->branch_id,
            'class_id' => $this->class_id,
            'report_type' => $this->report_type,
        ];

        if ($this->report_type === 'yearly' && $this->year) {
            $params['year'] = $this->year;
        }

        if ($this->report_type === 'custom' && $this->start_date && $this->end_date) {
            try {
                $startParts = explode('-', $this->start_date);
                $endParts = explode('-', $this->end_date);

                $params['start_date'] = (new Jalalian((int) $startParts[0], (int) $startParts[1], (int) $startParts[2]))
                    ->toCarbon()
                    ->format('Y-m-d');
                $params['end_date'] = (new Jalalian((int) $endParts[0], (int) $endParts[1], (int) $endParts[2]))
                    ->toCarbon()
                    ->format('Y-m-d');
            } catch (\Exception $e) {
                $params['start_date'] = now()->startOfMonth()->format('Y-m-d');
                $params['end_date'] = now()->endOfMonth()->format('Y-m-d');
            }
        }

        return route('financial-summary-report.generate', array_filter($params, fn($value) => filled($value)));
    }

    public function updatedYear($value)
    {
        // Handle year change safely without parsing issues
        if ($value && $this->report_type === 'yearly') {
            $this->year = $value;
        }
    }

    public function updatedReportType($value)
    {
        // Reset dates when report type changes
        if ($value === 'yearly') {
            $this->start_date = null;
            $this->end_date = null;
            // Ensure year is set for yearly reports
            if (!$this->year) {
                $this->year = Jalalian::now()->getYear();
            }
        } elseif ($value === 'quarterly') {
            $this->start_date = null;
            $this->end_date = null;
            $this->year = null;
        } elseif ($value === 'monthly') {
            $this->start_date = null;
            $this->end_date = null;
            $this->year = null;
        } elseif ($value === 'custom') {
            $now = Jalalian::now();
            $this->start_date = $now->getYear() . '-' . str_pad($now->getMonth(), 2, '0', STR_PAD_LEFT) . '-01';
            $this->end_date = $now->getYear() . '-' . str_pad($now->getMonth(), 2, '0', STR_PAD_LEFT) . '-' . str_pad($now->getMonthDays(), 2, '0', STR_PAD_LEFT);
            $this->year = null;
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
