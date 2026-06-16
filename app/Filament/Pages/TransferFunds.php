<?php
  
namespace App\Filament\Pages;

use App\Filament\Actions\ProcessDailyTransferAction;
use App\Models\Transaction;
use Filament\Pages\Page;

class TransferFunds extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static string $view = 'filament.pages.transfer-funds';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return 'Daily Balance';
    }

    protected function getHeaderActions(): array
    {
        return [
            ProcessDailyTransferAction::make(),
        ];
    }

    protected function getTodayIncome(): float
    {
        return (float) Transaction::where('type', 'income')
            ->whereDate('created_at', today())
            ->whereNull('daily_transfer_id')
            ->sum('amount');
    }

    protected function getViewData(): array
    {
        return [
            'todayIncome' => $this->getTodayIncome(),
            'destination' => config('app.default_transfer_destination'),
        ];
    }
}
