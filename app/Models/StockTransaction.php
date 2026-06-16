<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    protected static function booted()
    {
        static::creating(function ($transaction) {
            $material = $transaction->material;
            $material->updateStock($transaction->transaction_type, $transaction->quantity);
        });

        static::deleting(function ($transaction) {
            $material = $transaction->material;
            if ($transaction->transaction_type === 'add' || $transaction->transaction_type === 'return') {
                $material->decrement('stock_quantity', $transaction->quantity);
            } elseif ($transaction->transaction_type === 'sell' || $transaction->transaction_type === 'issue') {
                $material->increment('stock_quantity', $transaction->quantity);
            }
        });
    }
}
