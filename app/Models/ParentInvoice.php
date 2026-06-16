<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'parent_guardian_id',
        'family_code',
        'billing_month',
        'billing_year',
        'invoice_date',
        'due_date',
        'previous_balance',
        'subtotal',
        'total_amount',
        'paid_amount',
        'balance',
        'status',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'previous_balance' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (ParentInvoice $invoice): void {
            if (blank($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }
        });
    }

    public function parentGuardian(): BelongsTo
    {
        return $this->belongsTo(ParentGuardian::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ParentInvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ParentInvoicePayment::class);
    }

    public function recalculatePayments(): void
    {
        if ($this->status === 'cancelled') {
            return;
        }

        $paidAmount = (float) $this->payments()->sum('amount');
        $totalAmount = (float) $this->total_amount;
        $balance = max(0, $totalAmount - $paidAmount);

        $this->forceFill([
            'paid_amount' => $paidAmount,
            'balance' => $balance,
            'status' => match (true) {
                $paidAmount <= 0 => 'issued',
                $balance <= 0 => 'paid',
                default => 'partial',
            },
        ])->saveQuietly();
    }

    public static function generateInvoiceNumber(): string
    {
        $nextId = ((int) static::max('id')) + 1;

        do {
            $number = 'PINV-' . now()->format('Ym') . '-' . str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
            $nextId++;
        } while (static::where('invoice_number', $number)->exists());

        return $number;
    }
}
