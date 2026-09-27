<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'designation',
        'department',
        'father_name',
        'mother_name',
        'gender',
        'marital_status',
        'date_of_birth',
        'joining_date',
        'photo',
        'current_address',
        'permanent_address',
        'qualification',
        'work_experience',
        'note',
        'basic_salary',
        'contract_type',
        'work_from',
        'work_to',
        'title',
        'bank_account_number',
        'bank_name',
        'ifsc_code',
        'bank_branch',
    ];

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
