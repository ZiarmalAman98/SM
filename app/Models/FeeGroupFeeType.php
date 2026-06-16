<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;


class FeeGroupFeeType extends Pivot
{
    protected $table = 'fee_group_fee_types';

    protected $fillable = ['fee_group_id', 'fee_type_id', 'amount'];

    public function feeGroup(): BelongsTo
    {
        return $this->belongsTo(FeeGroup::class, 'fee_group_id');
    }

    /**
     * Get the Fee Type that this FeeGroupFeeType record belongs to.
     */
    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeeType::class, 'fee_type_id');
    }
}