<?php

namespace App\Filament\Widgets;

use App\Models\DailyTransfer;
use App\Services\DailyBalanceService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Morilog\Jalali\Jalalian;

class DailyBalanceWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $summary = app(DailyBalanceService::class)->todaySummary();
        $lastTransfer = DailyTransfer::query()->latest('id')->first();
        $destination = $lastTransfer?->destinationUser?->name
            ?? __('Not assigned');

        return [
            Stat::make(__("Today's Income"), 'AFN '.number_format($summary['income'], 2))
                ->description(__('Income + invoice payments'))
                ->color('success'),
            Stat::make(__("Today's Expenses"), 'AFN '.number_format($summary['expense'], 2))
                ->description(__('Cash paid out today'))
                ->color('danger'),
            Stat::make(__('Cash To Transfer'), 'AFN '.number_format($summary['net'], 2))
                ->description(__('Amount remaining in the drawer'))
                ->color('warning'),
            Stat::make(__('Last Transfer'), 'AFN '.number_format((float) ($lastTransfer?->amount ?? 0), 2))
                ->description(
                    $lastTransfer?->transfer_date
                        ? $destination.' — '.Jalalian::fromDateTime($lastTransfer->transfer_date)->format('Y/m/d')
                        : __('No transfers yet')
                ),
        ];
    }
}
