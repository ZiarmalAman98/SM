<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Transaction;
use Filament\Notifications\Notification;

class CreateIncome extends CreateRecord
{
    protected static string $resource = IncomeResource::class;

    protected function afterCreate(): void
    {
        $income = $this->record; // newly saved Income model

        // Create the ledger transaction for this income
        Transaction::create([
            'amount'            => (float) $income->amount,
            'type'              => 'income',
            'description'       => $income->description ?? ('Income #' . $income->id),
            // 'daily_transfer_id' => null, // optional; will default to NULL if column is nullable
        ]);

        Notification::make()
            ->title('Income Recorded')
            ->body('A matching income transaction has been created.')
            ->success()
            ->send();
    }
}
