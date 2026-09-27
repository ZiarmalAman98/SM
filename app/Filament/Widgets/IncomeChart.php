<?php

namespace App\Filament\Widgets;

use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class IncomeChart extends BarChartWidget
{
    protected static ?string $heading = null;

    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): string
    {
        return __('Approved Income by Period');
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => __('امروز'), // Today
            'week' => __('هفته جاری'), // This Week
            'month' => __('ماه جاری'), // This Month
            'year' => __('سال جاری'), // This Year
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['position' => 'bottom']],
            'scales' => ['y' => ['beginAtZero' => true]],
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? 'year';

        $query = DB::table('incomes')
            ->where('status', 'approved');

        if ($filter === 'year') {
            $currentJalaliYear = Jalalian::now()->getYear();

            $query->selectRaw('MONTH(date) as period, SUM(amount) as total')
                ->whereYear('date', Carbon::now()->year)
                ->groupBy('period')
                ->orderBy('period');

            // Dari month names
            $labels = [
                'حمل', 'ثور', 'جوزا',
                'سرطان', 'اسد', 'سنبله',
                'میزان', 'عقرب', 'قوس',
                'جدی', 'دلو', 'حوت'
            ];

            $totalPeriods = 12;
        } elseif ($filter === 'month') {
            $currentJalali = Jalalian::now();
            $daysInMonth = $currentJalali->getMonthDays();

            $query->selectRaw('DAY(date) as period, SUM(amount) as total')
                ->whereYear('date', Carbon::now()->year)
                ->whereMonth('date', Carbon::now()->month)
                ->groupBy('period')
                ->orderBy('period');

            $labels = range(1, $daysInMonth);
            $totalPeriods = $daysInMonth;
        } elseif ($filter === 'week') {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $query->selectRaw('DAY(date) as period, SUM(amount) as total')
                ->whereBetween('date', [$startOfWeek, $endOfWeek])
                ->groupBy('period')
                ->orderBy('period');

            // Dari day names
            $labels = [
                'شنبه', 'یکشنبه', 'دوشنبه',
                'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'
            ];

            $totalPeriods = 7;
        } else { // today
            $query->selectRaw('HOUR(date) as period, SUM(amount) as total')
                ->whereDate('date', Carbon::today())
                ->groupBy('period')
                ->orderBy('period');

            $labels = range(0, 23);
            $totalPeriods = 24;
        }

        $data = $query->pluck('total', 'period')->toArray();

        $chartData = [];
        $startIndex = ($filter === 'week') ? 1 : 0;
        $endIndex = $totalPeriods + $startIndex;

        for ($i = $startIndex; $i < $endIndex; $i++) {
            $chartData[] = $data[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => match ($filter) {
                        'year' => __('مجموع درآمد بر اساس ماه'), // Total Income by Month
                        'month' => __('مجموع درآمد بر اساس روز'), // Total Income by Day
                        'week' => __('مجموع درآمد بر اساس روزهای هفته'), // Total Income by Day of Week
                        'today' => __('مجموع درآمد بر اساس ساعت'), // Total Income by Hour
                        default => __('مجموع درآمد'), // Total Income
                    },
                    'data' => $chartData,
                    'backgroundColor' => '#2563eb',
                ],
            ],
            'labels' => $labels,
        ];
    }
}
