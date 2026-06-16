<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTransfer extends Model
{
    protected $fillable = ['amount', 'destination', 'reference', 'transfer_date'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function destinationUser()
    {
        return $this->belongsTo(User::class, 'destination_user_id');
    }

    protected $casts = [
        'transfer_date' => 'date', // ✅ ensures date formatting will work
    ];
}
