<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventorySaleItem extends Model
{
    protected $fillable = [
        'inventory_sale_id',
        'inventory_product_id',
        'quantity',
        'unit_price',
        'discount_percent',
        'discount_amount',
        'total_amount',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (InventorySaleItem $item): void {
            $grossTotal = max(0, (float) $item->quantity * (float) $item->unit_price);
            $discountPercent = min(100, max(0, (float) $item->discount_percent));
            $discountAmount = round($grossTotal * ($discountPercent / 100), 2);

            $item->discount_percent = $discountPercent;
            $item->discount_amount = $discountAmount;
            $item->total_amount = max(
                0,
                $grossTotal - $discountAmount,
            );
        });
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(InventorySale::class, 'inventory_sale_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryProduct::class, 'inventory_product_id');
    }
}
