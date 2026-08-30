<?php

namespace App\Filament\Pages;

use App\Filament\Actions\ProcessDailyTransferAction;
use App\Filament\Widgets\DailyBalanceWidget;
use App\Services\DailyBalanceService;
use Filament\Pages\Page;

class TransferFunds extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static string $view = 'filament.pages.transfer-funds';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return __('Daily Balance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Transfer Funds');
    }

    public function getTitle(): string
    {
        return __('Transfer Funds');
    }

    protected function getHeaderActions(): array
    {
        return [
            ProcessDailyTransferAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DailyBalanceWidget::class,
        ];
    }

    protected function getViewData(): array
    {
        $summary = app(DailyBalanceService::class)->todaySummary();

        return [
            'todayIncome' => $summary['income'],
            'todayExpense' => $summary['expense'],
            'todayNet' => $summary['net'],
            'destination' => config('app.default_transfer_destination'),
        ];
    }
}
