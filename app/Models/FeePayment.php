<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class FeePayment extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'receipt_number',
        'total_fees',
        'amount_paid',
        'month',
        'payment_date',
        'fee_type_id'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function getDuePaymentAttribute(): float
    {
        return max(0, $this->total_fees - $this->amount_paid);
    }
}
