<?php

namespace App\Filament\Widgets;

use Filament\Widgets\BarChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

class ExpenseChart extends BarChartWidget
{
    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return __('مصارف تائید شده بر اساس دوره');
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => __('امروز'),
            'week' => __('هفته جاری'),
            'month' => __('ماه جاری'),
            'year' => __('سال جاری'),
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? 'year';

        $query = DB::table('expenses')
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
                        'year' => __('مجموع مصارف بر اساس ماه'),
                        'month' => __('مجموع مصارف بر اساس روز'),
                        'week' => __('مجموع مصارف بر اساس روزهای هفته'),
                        'today' => __('مجموع مصارف بر اساس ساعت'),
                        default => __('مجموع مصارف'),
                    },
                    'data' => $chartData,
                    'backgroundColor' => '#dc2626', // Red color for expenses
                ],
            ],
            'labels' => $labels,
        ];
    }
}