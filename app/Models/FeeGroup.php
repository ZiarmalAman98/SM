<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeGroup extends Model
{
    use HasFactory;
    protected $fillable = ['group_name', 'description'];

    public function feeTypes(): BelongsToMany
    {
        return $this->belongsToMany(FeeType::class, 'fee_group_fee_types')
            ->withPivot('amount') // Ensure pivot data is accessible
            ->withTimestamps();
    }
    
    public function feeGroupFeeTypes(): HasMany
    {
        return $this->hasMany(FeeGroupFeeType::class, 'fee_group_id');
    }

    public function feeGroupAssignments(): HasMany
    {
        return $this->hasMany(FeeGroupAssignment::class, 'fee_group_id');
    }
}
