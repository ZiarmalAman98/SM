<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Transaction;

class EditIncome extends EditRecord
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * After the income record is updated, sync the transaction.
     */
    protected function afterSave(): void
    {
        $income = $this->record;

        // Find existing transaction by description or by link (better if you store income_id in transactions)
        $transaction = Transaction::where('type', 'income')
            ->where('description', 'like', 'Income #' . $income->id . '%')
            ->first();

        if ($transaction) {
            // Update existing transaction
            $transaction->update([
                'amount'      => (float) $income->amount,
                'description' => $income->description ?? ('Income #' . $income->id),
            ]);
        } else {
            // If no transaction exists, create a new one
            Transaction::create([
                'amount'      => (float) $income->amount,
                'type'        => 'income',
                'description' => $income->description ?? ('Income #' . $income->id),
                // daily_transfer_id left NULL (default)
            ]);
        }
    }
}
