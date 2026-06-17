<?php

namespace App\Filament\Resources\ParentInvoicePaymentResource\Pages;

use App\Filament\Resources\ParentInvoicePaymentResource;
use App\Models\ParentInvoice;
use App\Models\ParentInvoicePayment;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateParentInvoicePayment extends CreateRecord
{
    protected static string $resource = ParentInvoicePaymentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): ParentInvoicePayment {
            $selectedInvoice = ParentInvoice::findOrFail($data['parent_invoice_id']);
            $remainingAmount = (float) $data['amount'];
            $baseReceiptNumber = $data['receipt_number'] ?: ParentInvoicePayment::generateReceiptNumber();
            $createdPayments = collect();

            $invoices = ParentInvoice::query()
                ->where('parent_guardian_id', $selectedInvoice->parent_guardian_id)
                ->where('status', '!=', 'cancelled')
                ->where('balance', '>', 0)
                ->get()
                ->sortBy(fn(ParentInvoice $invoice): string => sprintf(
                    '%04d%02d%010d',
                    (int) $invoice->billing_year,
                    ParentInvoicePaymentResource::billingMonthNumber($invoice->billing_month),
                    (int) $invoice->id,
                ));

            foreach ($invoices as $index => $invoice) {
                if ($remainingAmount <= 0) {
                    break;
                }

                $paymentAmount = min($remainingAmount, (float) $invoice->balance);

                if ($paymentAmount <= 0) {
                    continue;
                }

                $createdPayments->push(ParentInvoicePayment::create([
                    ...$data,
                    'parent_invoice_id' => $invoice->id,
                    'receipt_number' => $this->receiptNumberForAllocation($baseReceiptNumber, $createdPayments->count()),
                    'amount' => $paymentAmount,
                ]));

                $remainingAmount -= $paymentAmount;
            }

            if ($remainingAmount > 0) {
                $createdPayments->push(ParentInvoicePayment::create([
                    ...$data,
                    'parent_invoice_id' => $selectedInvoice->id,
                    'receipt_number' => $this->receiptNumberForAllocation($baseReceiptNumber, $createdPayments->count()),
                    'amount' => $remainingAmount,
                ]));
            }

            $firstPayment = $createdPayments->first();

            if (! $firstPayment) {
                throw ValidationException::withMessages([
                    'amount' => __('Payment amount must be greater than zero.'),
                ]);
            }

            return $firstPayment;
        });
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $invoice = ParentInvoice::findOrFail($data['parent_invoice_id']);
        $amount = (float) $data['amount'];

        if ($invoice->status === 'cancelled') {
            throw ValidationException::withMessages([
                'parent_invoice_id' => __('Cannot pay a cancelled invoice.'),
            ]);
        }

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => __('Payment amount must be greater than zero.'),
            ]);
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return ParentInvoicePaymentResource::getUrl('index');
    }

    private function receiptNumberForAllocation(string $baseReceiptNumber, int $index): string
    {
        return $index === 0 ? $baseReceiptNumber : "{$baseReceiptNumber}-" . ($index + 1);
    }
}
