<?php

namespace App\Models;

use App\Services\DailyBalanceService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(fn (Expense $expense) => app(DailyBalanceService::class)->syncExpense($expense));
        static::deleted(fn (Expense $expense) => app(DailyBalanceService::class)->removeSource($expense));
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }
}
