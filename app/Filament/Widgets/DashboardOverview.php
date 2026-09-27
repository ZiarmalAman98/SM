<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Income;
use App\Models\ParentInvoice;
use App\Models\ParentInvoicePayment;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Morilog\Jalali\Jalalian;

class DashboardOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = true;

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {
        // Fetch user counts
        $totalStudents = User::where('type', 'student')->count();
        $totalTeachers = User::where('type', 'teacher')->count();
        $totalStaff = User::where('type', 'staff')->count();

        // Gender-based counts
        $totalMaleStudents = User::where('type', 'student')
            ->whereHas('student', fn($query) => $query->whereRaw('LOWER(gender) = ?', ['male']))
            ->count();

        $totalFemaleStudents = User::where('type', 'student')
            ->whereHas('student', fn($query) => $query->whereRaw('LOWER(gender) = ?', ['female']))
            ->count();

        $totalMaleEmp = User::whereIn('type', ['staff', 'teacher'])
            ->where(function ($query) {
                $query->whereHas('staff', fn($staff) => $staff->whereRaw('LOWER(gender) = ?', ['male']))
                    ->orWhereHas('teacher', fn($teacher) => $teacher->whereRaw('LOWER(gender) = ?', ['male']));
            })
            ->count();

        $totalFemaleEmp = User::whereIn('type', ['staff', 'teacher'])
            ->where(function ($query) {
                $query->whereHas('staff', fn($staff) => $staff->whereRaw('LOWER(gender) = ?', ['female']))
                    ->orWhereHas('teacher', fn($teacher) => $teacher->whereRaw('LOWER(gender) = ?', ['female']));
            })
            ->count();

        // Classes and sections
        $totalClasses = SchoolClass::count();
        $totalSections = Section::count();

        // Jalali date handling
        $currentJalali = Jalalian::now();
        $currentJalaliMonthName = $currentJalali->format('F'); // e.g., "مهر"
        $currentJalaliYear = $currentJalali->getYear();

        // Convert Jalali month to Gregorian range for DB queries
        $startOfMonth = $currentJalali->toCarbon()->startOfMonth();
        $endOfMonth = $currentJalali->toCarbon()->endOfMonth();

        // Income (filtered by Gregorian dates)
        $nowJ = Jalalian::fromCarbon(now());
        $jy   = $nowJ->getYear();
        $jm   = $nowJ->getMonth();

        // Start of this Jalali month (to Gregorian)
        $startOfJMonth = (new Jalalian($jy, $jm, 1))
            ->toCarbon()
            ->startOfDay();

        // End of this Jalali month
        $endOfJMonth = (new Jalalian($jy, $jm, 1))
            ->addMonths()
            ->subDays(1)
            ->toCarbon()
            ->endOfDay();

        // Approved Incomes (this Jalali month)
        $totalApprovedIncome = Income::where('status', 'approved')
            ->whereBetween('date', [$startOfJMonth, $endOfJMonth])
            ->sum('amount');

        $approvedIncomeBadge = $this->getBadgeColor(
            $totalApprovedIncome,
            [20000 => 'success', 50000 => 'primary']
        );

        // Approved Expenses (this Jalali month)
        $totalApprovedExpenses = Expense::where('status', 'approved')
            ->whereBetween('date', [$startOfJMonth, $endOfJMonth])
            ->sum('amount');

        $approvedExpenseBadge = $this->getBadgeColor(
            $totalApprovedExpenses,
            [20000 => 'success', 50000 => 'primary']
        );

        // Invoice payments for this Jalali month
        // Current time -> Jalali (no arrays involved)
        $nowJ = Jalalian::fromCarbon(now());
        $jy   = $nowJ->getYear();
        $jm   = $nowJ->getMonth();

        // Start of this Jalali month (to Gregorian)
        $startOfJMonth = (new Jalalian($jy, $jm, 1))
            ->toCarbon()
            ->startOfDay();

        // End of this Jalali month (first day of next J-month minus 1 day)
        $endOfJMonth = (new Jalalian($jy, $jm, 1))
            ->addMonths()
            ->subDays(1)
            ->toCarbon()
            ->endOfDay();

        // Sum invoice payments within this Jalali month (purely by Gregorian date range)
        $totalInvoicePayments = ParentInvoicePayment::query()
            ->whereBetween('payment_date', [$startOfJMonth, $endOfJMonth])
            ->sum('amount');

        $invoicePaymentsBadge = $this->getBadgeColor($totalInvoicePayments, [
            20000 => 'success',
            50000 => 'primary',
        ]);

        $studentInvoiceQuery = ParentInvoice::query()
            ->where('status', '!=', 'cancelled');

        $totalStudentInvoiceAmount = (float) (clone $studentInvoiceQuery)->sum('total_amount');
        $totalStudentReceivedAmount = (float) (clone $studentInvoiceQuery)->sum('paid_amount');
        $totalStudentBalanceDue = (float) (clone $studentInvoiceQuery)->sum('balance');

        return [
            Stat::make(__('Total Students'), number_format($totalStudents))
                ->description(__('Total enrolled students'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make(__('Total Teachers'), number_format($totalTeachers))
                ->description(__('Total teaching staff'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make(__('Total Staff'), number_format($totalStaff))
                ->description(__('Total non-teaching staff'))
                ->descriptionIcon('heroicon-m-building-office')
                ->color('gray'),

            Stat::make(__('Total Classes'), number_format($totalClasses))
                ->description(__('Total school classes'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),

            Stat::make(__('Total Sections'), number_format($totalSections))
                ->description(__('Total sections'))
                ->descriptionIcon('heroicon-m-squares-2x2')
                ->color('info'),

            Stat::make(__('Total Income This Month'), number_format($totalApprovedIncome, 2) . ' AFN')
                ->description(__("Approved income | {$currentJalaliMonthName} {$currentJalaliYear}"))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($approvedIncomeBadge),

            Stat::make(__('Total Expenses This Month'), number_format($totalApprovedExpenses, 2) . ' AFN')
                ->description(__("Approved expenses | {$currentJalaliMonthName} {$currentJalaliYear}"))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color($approvedExpenseBadge),

            Stat::make(__('Invoice Payments This Month'), number_format($totalInvoicePayments, 2) . ' AFN')
                ->description(__("Payments received | {$currentJalaliMonthName} {$currentJalaliYear}"))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($invoicePaymentsBadge),

            Stat::make(__('Student Fees Received'), number_format($totalStudentReceivedAmount, 2) . ' AFN')
                ->description(__('Total received from student invoices'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make(__('Student Balance Due'), number_format($totalStudentBalanceDue, 2) . ' AFN')
                ->description(__('Outstanding student invoice balance'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalStudentBalanceDue > 0 ? 'danger' : 'success'),
        ];
    }

    /**
     * Helper method to determine badge color based on thresholds
     */
    protected function getBadgeColor(float $value, array $thresholds): string
    {
        if ($value <= 0) {
            return 'gray';
        }

        if ($value >= ($thresholds[50000] ?? PHP_FLOAT_MAX)) {
            return 'primary';
        }
        if ($value >= ($thresholds[20000] ?? 0)) {
            return 'success';
        }
        return 'danger';
    }
}
