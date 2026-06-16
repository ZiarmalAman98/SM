<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FeeType extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'default_amount'];

    public function feeGroups(): BelongsToMany
    {
        return $this->belongsToMany(FeeGroup::class, 'fee_group_fee_types')
            ->withPivot('amount')
            ->withTimestamps();
    }
}
