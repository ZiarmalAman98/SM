<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $casts = [
        'min_amount' => 'integer',
        'max_amount' => 'integer',
        'fixed_amount' => 'integer',
        'percentage' => 'float',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->where('type', 'staff');
    }
}
