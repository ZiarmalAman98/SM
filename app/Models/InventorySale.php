<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InventorySale extends Model
{
    protected $fillable = [
        'sale_no',
        'sale_type',
        'payment_destination',
        'student_id',
        'parent_guardian_id',
        'parent_invoice_id',
        'customer_name',
        'sale_date',
        'subtotal',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'balance',
        'status',
        'notes',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (InventorySale $sale): void {
            if (blank($sale->sale_no)) {
                $sale->sale_no = static::generateSaleNo();
            }
        });

        static::deleted(fn(InventorySale $sale) => $sale->stockMovements()->delete());
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function parentGuardian(): BelongsTo
    {
        return $this->belongsTo(ParentGuardian::class);
    }

    public function parentInvoice(): BelongsTo
    {
        return $this->belongsTo(ParentInvoice::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventorySaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InventorySalePayment::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(InventoryStockMovement::class, 'reference');
    }

    public static function generateSaleNo(): string
    {
        $nextId = ((int) static::max('id')) + 1;

        do {
            $number = 'SAL-' . now()->format('Ym') . '-' . str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
            $nextId++;
        } while (static::where('sale_no', $number)->exists());

        return $number;
    }
}
