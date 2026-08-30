<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    protected $fillable = [
        'amount',
        'type',
        'description',
        'daily_transfer_id',
        'source_type',
        'source_id',
        'occurred_on',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'occurred_on' => 'date',
    ];

    public function dailyTransfer(): BelongsTo
    {
        return $this->belongsTo(DailyTransfer::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}
