<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventorySalePayment extends Model
{
    protected $fillable = [
        'inventory_sale_id',
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
        static::creating(function (InventorySalePayment $payment): void {
            if (blank($payment->receipt_number)) {
                $payment->receipt_number = static::generateReceiptNumber();
            }
        });
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(InventorySale::class, 'inventory_sale_id');
    }

    public static function generateReceiptNumber(): string
    {
        $nextId = ((int) static::max('id')) + 1;

        do {
            $number = 'ISR-' . now()->format('Ym') . '-' . str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
            $nextId++;
        } while (static::where('receipt_number', $number)->exists());

        return $number;
    }
}
