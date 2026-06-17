<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class InventoryStockMovement extends Model
{
    protected $fillable = [
        'inventory_product_id',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'movement_date',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'movement_date' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(InventoryProduct::class, 'inventory_product_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
