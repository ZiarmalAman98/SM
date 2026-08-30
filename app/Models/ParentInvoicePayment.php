<?php

namespace App\Models;

use App\Services\DailyBalanceService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentInvoicePayment extends Model
{
    protected $fillable = [
        'parent_invoice_id',
        'receipt_number',
        'amount',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (ParentInvoicePayment $payment): void {
            if (blank($payment->receipt_number)) {
                $payment->receipt_number = static::generateReceiptNumber();
            }
        });

        static::saved(function (ParentInvoicePayment $payment): void {
            $payment->invoice?->recalculatePayments();
            app(DailyBalanceService::class)->syncInvoicePayment($payment);
        });
        static::deleted(function (ParentInvoicePayment $payment): void {
            $payment->invoice?->recalculatePayments();
            app(DailyBalanceService::class)->removeSource($payment);
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(ParentInvoice::class, 'parent_invoice_id');
    }

    public static function generateReceiptNumber(): string
    {
        $nextId = ((int) static::max('id')) + 1;

        do {
            $number = 'PIP-' . now()->format('Ym') . '-' . str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
            $nextId++;
        } while (static::where('receipt_number', $number)->exists());

        return $number;
    }
}
