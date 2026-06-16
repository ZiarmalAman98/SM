<?php

namespace App\Filament\Resources\ParentInvoicePaymentResource\Pages;

use App\Filament\Resources\ParentInvoicePaymentResource;
use App\Models\ParentInvoice;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateParentInvoicePayment extends CreateRecord
{
    protected static string $resource = ParentInvoicePaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $invoice = ParentInvoice::findOrFail($data['parent_invoice_id']);
        $amount = (float) $data['amount'];

        if ($invoice->status === 'cancelled') {
            throw ValidationException::withMessages([
                'parent_invoice_id' => __('Cannot pay a cancelled invoice.'),
            ]);
        }

        if ($amount <= 0 || $amount > (float) $invoice->balance) {
            throw ValidationException::withMessages([
                'amount' => __('Payment amount must be greater than zero and not more than the remaining balance.'),
            ]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return ParentInvoicePaymentResource::getUrl('index');
    }
}
