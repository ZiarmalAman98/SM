<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryProduct extends Model
{
    protected $fillable = [
        'inventory_category_id',
        'name',
        'sku',
        'unit',
        'purchase_price',
        'sale_price',
        'min_stock',
        'is_active',
        'description',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (InventoryProduct $product): void {
            if (blank($product->sku)) {
                $product->sku = static::generateSku();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(InventoryStockMovement::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(InventoryPurchaseItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(InventorySaleItem::class);
    }

    public function hasInventoryHistory(): bool
    {
        return $this->purchaseItems()->exists()
            || $this->saleItems()->exists()
            || $this->stockMovements()->exists();
    }

    public function getCurrentStockAttribute(): float
    {
        $in = (float) $this->stockMovements()
            ->whereIn('type', ['purchase_in', 'adjustment_in', 'return_in'])
            ->sum('quantity');

        $out = (float) $this->stockMovements()
            ->whereIn('type', ['sale_out', 'adjustment_out', 'return_out'])
            ->sum('quantity');

        return $in - $out;
    }

    public static function generateSku(): string
    {
        $nextId = ((int) static::max('id')) + 1;

        do {
            $sku = 'PRD-' . str_pad((string) $nextId, 5, '0', STR_PAD_LEFT);
            $nextId++;
        } while (static::where('sku', $sku)->exists());

        return $sku;
    }
}
