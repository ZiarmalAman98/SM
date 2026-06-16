<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    public function stockType()
    {
        return $this->belongsTo(StockType::class);
    }

    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function updateStock(string $transactionType, int $quantity): void
    {
        if ($transactionType === 'add' || $transactionType === 'return') {
            $this->increment('stock_quantity', $quantity);
        } elseif ($transactionType === 'sell' || $transactionType === 'issue') {
            if ($this->stock_quantity < $quantity) {
                throw new \Exception('Insufficient stock to complete the transaction.');
            }
            $this->decrement('stock_quantity', $quantity);
        }
    }
}
