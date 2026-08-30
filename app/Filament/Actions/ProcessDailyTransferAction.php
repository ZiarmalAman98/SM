<?php

namespace App\Filament\Actions;

use App\Models\User;
use App\Services\DailyBalanceService;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;

class ProcessDailyTransferAction
{
    public static function make(): Action
    {
        return Action::make('process_daily_transfer')
            ->label(__('Process Daily Transfer'))
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(__('Process Daily Transfer'))
            ->modalDescription(__('This will transfer today\'s remaining cash (income + invoice payments − expenses) to the selected staff.'))
            ->modalSubmitActionLabel(__('Confirm Transfer'))
            ->form([
                Forms\Components\Select::make('destination_user_id')
                    ->label(__('Destination Staff'))
                    ->options(
                        fn () => User::query()
                            ->where('type', 'staff')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),
            ])
            ->action(function (array $data): void {
                $summary = app(DailyBalanceService::class)->todaySummary();

                if ($summary['net'] <= 0) {
                    Notification::make()
                        ->title(__('No Cash To Transfer'))
                        ->body(__('Today\'s remaining cash is zero. Record income or invoice payments first, or expenses already used the cash.'))
                        ->warning()
                        ->send();

                    return;
                }

                try {
                    $transfer = app(DailyBalanceService::class)->processTransfer((int) $data['destination_user_id']);

                    Notification::make()
                        ->title(__('Transfer Successful'))
                        ->body(__('Transferred :amount to daily transfer :reference.', [
                            'amount' => 'AFN '.number_format((float) $transfer->amount, 2),
                            'reference' => $transfer->reference,
                        ]))
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title(__('Transfer Failed'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
