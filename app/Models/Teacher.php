<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Teacher extends Model
{
    protected $casts = [
        'dob' => 'date',
        'joining_date' => 'date',
        'date_of_birth' => 'date',
        'joining_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->where('type', 'teacher');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }
}
