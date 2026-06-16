<?php

namespace App\Filament\Resources\ParentInvoicePaymentResource\Pages;

use App\Filament\Resources\ParentInvoicePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditParentInvoicePayment extends EditRecord
{
    protected static string $resource = ParentInvoicePaymentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $invoice = $this->record->invoice;
        $amount = (float) $data['amount'];
        $maxPayable = (float) $invoice->balance + (float) $this->record->amount;

        if ($invoice->status === 'cancelled') {
            throw ValidationException::withMessages([
                'parent_invoice_id' => __('Cannot update payment for a cancelled invoice.'),
            ]);
        }

        if ($amount <= 0 || $amount > $maxPayable) {
            throw ValidationException::withMessages([
                'amount' => __('Payment amount must be greater than zero and not more than the remaining balance.'),
            ]);
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(__('Print Receipt'))
                ->icon('heroicon-o-printer')
                ->url(fn() => route('parent-invoice-payments.print', $this->record))
                ->openUrlInNewTab(),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
