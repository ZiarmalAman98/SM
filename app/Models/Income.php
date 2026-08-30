<?php

namespace App\Models;

use App\Services\DailyBalanceService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    protected $casts = [
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(fn (Income $income) => app(DailyBalanceService::class)->syncIncome($income));
        static::deleted(fn (Income $income) => app(DailyBalanceService::class)->removeSource($income));
    }

    public function source()
    {
        return $this->belongsTo(IncomeSource::class);
    }
}
