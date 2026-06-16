<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['amount', 'type', 'description', 'daily_transfer_id'];

    public function dailyTransfer()
    {
        return $this->belongsTo(DailyTransfer::class);
    }
}
