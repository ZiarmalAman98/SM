<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeDiscount extends Model
{
    use HasFactory;
    protected $fillable = ['discount_name', 'discount_code', 'discount_value'];

    public function feeGroupAssignments(): HasMany
    {
        return $this->hasMany(FeeGroupAssignment::class, 'fee_discount_id');
    }
}