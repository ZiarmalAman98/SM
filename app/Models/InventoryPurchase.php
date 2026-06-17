<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InventoryPurchase extends Model
{
    protected $fillable = [
        'purchase_no',
        'inventory_supplier_id',
        'purchase_date',
        'subtotal',
        'discount_percent',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'payment_method',
        'balance',
        'status',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (InventoryPurchase $purchase): void {
            if (blank($purchase->purchase_no)) {
                $purchase->purchase_no = static::generatePurchaseNo();
            }
        });

        static::deleted(fn(InventoryPurchase $purchase) => $purchase->stockMovements()->delete());
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(InventorySupplier::class, 'inventory_supplier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryPurchaseItem::class);
    }

    public function stockMovements(): MorphMany
    {
        return $this->morphMany(InventoryStockMovement::class, 'reference');
    }

    public static function generatePurchaseNo(): string
    {
        $nextId = ((int) static::max('id')) + 1;

        do {
            $number = 'PUR-' . now()->format('Ym') . '-' . str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
            $nextId++;
        } while (static::where('purchase_no', $number)->exists());

        return $number;
    }
}
