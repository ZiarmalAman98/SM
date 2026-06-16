<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }
    protected $casts = [
    'payment_date' => 'date',
    ];
}
