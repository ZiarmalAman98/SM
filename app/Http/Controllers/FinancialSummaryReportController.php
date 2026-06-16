<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\ParentInvoice;
use App\Models\ParentInvoicePayment;
use App\Models\Payroll;
use App\Models\Income;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class FinancialSummaryReportController extends Controller
{
    public function generate(Request $request)
    {
        $branchId = $request->get('branch_id');
        $classId = $request->get('class_id');
        $reportType = $request->get('report_type', 'monthly');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $year = $request->get('year');

        // Set default year if not provided
        if (!$year) {
            $year = Jalalian::now()->getYear();
        }

        // Get branch information
        $branch = Branch::findOrFail($branchId);
        $class = $classId ? SchoolClass::findOrFail($classId) : null;

        // Calculate date range based on report type
        $dateRange = $this->calculateDateRange($reportType, $startDate, $endDate, $year);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Get financial data
        $financialData = $this->getFinancialData($branchId, $classId, $start, $end);

        $appSettings = appReportSettings();

        return view('print.financial-summary-report', [
            'branch' => $branch,
            'class' => $class,
            'reportType' => $reportType,
            'startDate' => $start,
            'endDate' => $end,
            'financialData' => $financialData,
            'settings' => $appSettings,
            'schoolName' => $appSettings['app_name'],
            'appLogo' => $appSettings['app_logo'] ?? null,
            'appLogoUrl' => $appSettings['app_logo_url'],
            'schoolAddress' => $appSettings['address'],
            'startDateJalali' => Jalalian::fromCarbon($start),
            'endDateJalali' => Jalalian::fromCarbon($end),
            'generatedDateJalali' => Jalalian::fromCarbon(now()),
        ]);
    }

    private function calculateDateRange($reportType, $startDate, $endDate, $year)
    {
        switch ($reportType) {
            case 'monthly':
                return [
                    'start' => now()->startOfMonth(),
                    'end' => now()->endOfMonth()
                ];
            case 'quarterly':
                $quarter = ceil(now()->month / 3);
                return [
                    'start' => now()->startOfYear()->addMonths(($quarter - 1) * 3),
                    'end' => now()->startOfYear()->addMonths($quarter * 3)->subDay()
                ];
            case 'yearly':
                // For yearly reports, use a simple approach that avoids Jalali parsing issues
                // Approximate conversion: Jalali year is roughly 621 years behind Gregorian
                // But we'll use a more accurate method by getting the current year difference
                try {
                    $currentJalali = Jalalian::now();
                    $currentGregorian = $currentJalali->toCarbon();

                    // Calculate the difference between the requested year and current Jalali year
                    $yearDifference = (int)$year - $currentJalali->getYear();

                    // Apply the difference to the current Gregorian year
                    $gregorianYear = $currentGregorian->year + $yearDifference;

                    // Ensure we have a valid year
                    if ($gregorianYear < 1900 || $gregorianYear > 2100) {
                        throw new \InvalidArgumentException('Invalid year range');
                    }

                    return [
                        'start' => Carbon::createFromDate($gregorianYear)->startOfYear(),
                        'end' => Carbon::createFromDate($gregorianYear)->endOfYear()
                    ];
                } catch (\Exception $e) {
                    // Fallback to current year if anything fails
                    return [
                        'start' => now()->startOfYear(),
                        'end' => now()->endOfYear()
                    ];
                }
            case 'custom':
                if (!$startDate || !$endDate) {
                    throw new \InvalidArgumentException('Start date and end date are required for custom reports');
                }
                return [
                    'start' => Carbon::parse($startDate),
                    'end' => Carbon::parse($endDate)
                ];
            default:
                return [
                    'start' => now()->startOfMonth(),
                    'end' => now()->endOfMonth()
                ];
        }
    }

    private function getFinancialData($branchId, $classId, $start, $end)
    {
        // Invoice payments
        $feeQuery = ParentInvoicePayment::query()
            ->with(['invoice.parentGuardian.user'])
            ->whereBetween('payment_date', [$start, $end]);
        if ($classId) {
            $feeQuery->whereHas('invoice.items', fn($query) => $query->where('class_id', $classId));
        } elseif ($branchId) {
            $feeQuery->whereHas('invoice.items.schoolClass', fn($query) => $query->where('branch_id', $branchId));
        }

        $feePayments = $feeQuery->get();
        $totalFeesCollected = $feePayments->sum('amount');

        $invoiceQuery = ParentInvoice::query()
            ->with(['parentGuardian.user', 'items.schoolClass'])
            ->whereBetween('invoice_date', [$start, $end]);
        if ($classId) {
            $invoiceQuery->whereHas('items', fn($query) => $query->where('class_id', $classId));
        } elseif ($branchId) {
            $invoiceQuery->whereHas('items.schoolClass', fn($query) => $query->where('branch_id', $branchId));
        }

        $invoices = $invoiceQuery->get();
        $totalFeesDue = $invoices->sum('total_amount');
        $outstandingFees = $invoices->sum('balance');

        // Payroll Expenses
        $payrollQuery = Payroll::whereBetween('created_at', [$start, $end]);
        $payrolls = $payrollQuery->get();
        $totalPayrollExpenses = $payrolls->sum('net_salary');
        $totalBonuses = $payrolls->sum('bonus');
        $totalDeductions = $payrolls->sum('deductions');

        // Income
        $incomeQuery = Income::whereBetween('date', [$start, $end])
            ->where('status', 'approved');
        $incomes = $incomeQuery->get();
        $totalIncome = $incomes->sum('amount');

        // Calculate net profit/loss
        $totalRevenue = $totalFeesCollected + $totalIncome;
        $totalExpenses = $totalPayrollExpenses;
        $netProfit = $totalRevenue - $totalExpenses;

        // Monthly breakdown for charts
        $monthlyData = $this->getMonthlyBreakdown($start, $end, $branchId, $classId);

        return [
            'feePayments' => $feePayments,
            'invoices' => $invoices,
            'totalFeesCollected' => $totalFeesCollected,
            'totalFeesDue' => $totalFeesDue,
            'outstandingFees' => $outstandingFees,
            'payrolls' => $payrolls,
            'totalPayrollExpenses' => $totalPayrollExpenses,
            'totalBonuses' => $totalBonuses,
            'totalDeductions' => $totalDeductions,
            'incomes' => $incomes,
            'totalIncome' => $totalIncome,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'monthlyData' => $monthlyData,
        ];
    }

    private function getMonthlyBreakdown($start, $end, $branchId, $classId)
    {
        $months = [];
        $current = $start->copy();

        while ($current->lte($end)) {
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            // Invoice payments for this month
            $feeQuery = ParentInvoicePayment::whereBetween('payment_date', [$monthStart, $monthEnd]);
            if ($classId) {
                $feeQuery->whereHas('invoice.items', fn($query) => $query->where('class_id', $classId));
            } elseif ($branchId) {
                $feeQuery->whereHas('invoice.items.schoolClass', fn($query) => $query->where('branch_id', $branchId));
            }
            $monthFees = $feeQuery->sum('amount');

            // Payroll for this month
            $monthPayroll = Payroll::whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('net_salary');

            // Income for this month
            $monthIncome = Income::whereBetween('date', [$monthStart, $monthEnd])
                ->where('status', 'approved')
                ->sum('amount');

            $months[] = [
                'month' => Jalalian::fromCarbon($current)->format('F Y'),
                'fees' => $monthFees,
                'payroll' => $monthPayroll,
                'income' => $monthIncome,
                'net' => ($monthFees + $monthIncome) - $monthPayroll
            ];

            $current->addMonth();
        }

        return $months;
    }
}
