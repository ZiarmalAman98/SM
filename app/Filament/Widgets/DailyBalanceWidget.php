<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\DailyTransfer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Morilog\Jalali\Jalalian;

class DailyBalanceWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalIncome = Transaction::where('type', 'income')
            ->whereDate('created_at', today())
            ->whereNull('daily_transfer_id')
            ->sum('amount');

        $lastTransfer = DailyTransfer::latest()->first();

        return [
            Stat::make("Today's Balance", '؋ ' . number_format($totalIncome, 2))
                ->description('Amount to be transferred at EOD'),

            Stat::make('Last Transfer Amount', '؋ ' . number_format($lastTransfer?->amount ?? 0, 2))
                ->description(
                    $lastTransfer && $lastTransfer->transfer_date
                        ? 'On ' . Jalalian::fromDateTime($lastTransfer->transfer_date)->format('%A %d %B %Y')
                        : 'No transfers yet'
                ),

            Stat::make('Last Transfer Destination', $lastTransfer?->destination ?? 'N/A'),
        ];
    }
}
