<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
class FeeGroupAssignment extends Model
{
    protected $fillable = [
        'fee_group_id',
        'assignable_id',
        'assignable_type',
        'effective_date',
        'fee_discount_id',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    /**
     * Get the fee group associated with this assignment.
     */
    public function feeGroup(): BelongsTo
    {
        return $this->belongsTo(FeeGroup::class, 'fee_group_id');
    }

    /**
     * Get the fee discount associated with this assignment (if any).
     */
    public function feeDiscount(): BelongsTo
    {
        return $this->belongsTo(FeeDiscount::class, 'fee_discount_id');
    }

    /**
     * Get the model (Student, Teacher, etc.) assigned to this fee group.
     */
    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }
}
