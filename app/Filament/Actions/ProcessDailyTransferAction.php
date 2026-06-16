<?php

namespace App\Filament\Actions;

use App\Models\DailyTransfer;
use App\Models\Transaction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Filament\Forms;


class ProcessDailyTransferAction
{
    public static function make(): Action
    {
        return Action::make('process_daily_transfer')
            ->label('Process Daily Transfer')
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Process Daily Transfer')
            ->modalDescription('This will transfer all unprocessed income from today to the destination account.')
            ->modalSubmitActionLabel('Confirm Transfer')
            ->form([
                Forms\Components\Select::make('destination_user_id')
                    ->label('Destination Staff')
                    ->options(
                        fn () => User::query()
                            ->where('type', 'staff') // ✅ exclude teacher, student, guardian etc.
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),
            ])
            ->action(function (array $data) {
            try {
                // 1) Guard BEFORE transaction
                $totalIncome = Transaction::where('type', 'income')
                    ->whereDate('created_at', today())
                    ->whereNull('daily_transfer_id')
                    ->sum('amount');

                if ($totalIncome <= 0) {
                    Notification::make()
                        ->title('No Income Found')
                        ->body('There is no income to transfer today.')
                        ->warning()
                        ->send();
                    return; // ✅ stop action here; prevents success toast
                }

                // 2) Do the work atomically
                DB::transaction(function () use ($data, $totalIncome) {
                    $transfer = DailyTransfer::create([
                        'amount'              => $totalIncome,
                        'destination_user_id' => $data['destination_user_id'],
                        'reference'           => 'DAILY-' . now()->format('Ymd'),
                        'transfer_date'       => now(),
                    ]);

                    Transaction::create([
                        'amount'            => $totalIncome,
                        'type'              => 'transfer_out',
                        'description'       => 'Daily transfer to staff #' . $transfer->destination_user_id,
                        'daily_transfer_id' => $transfer->id,
                        // 'user_id'        => null, // or system user id if you have one
                    ]);

                    Transaction::create([
                        'amount'            => $totalIncome,
                        'type'              => 'transfer_in',
                        'description'       => 'Daily income transfer received',
                        'daily_transfer_id' => $transfer->id,
                        // 'user_id'           => $transfer->destination_user_id, // credit staff
                    ]);

                    Transaction::where('type', 'income')
                        ->whereDate('created_at', today())
                        ->whereNull('daily_transfer_id')
                        ->update(['daily_transfer_id' => $transfer->id]);
                });

                // 3) Show success only if we actually transferred
                Notification::make()
                    ->title('Transfer Successful')
                    ->body('All daily income has been transferred.')
                    ->success()
                    ->send();

            } catch (\Exception $e) {
                Notification::make()
                    ->title('Transfer Failed')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
                throw $e;
            }
        });

    }
}

