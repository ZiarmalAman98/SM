<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AdmissionInquiry extends Model
{
    protected $fillable = [
        'inquiry_date',
        'parent_name',
        'phone_number',
        'email',
        'address',
        'child_name',
        'child_dob',
        'interested_class_id',
        'inquiry_status',
        'notes',
    ];

    protected $casts = [
        'inquiry_date' => 'datetime',
        'child_dob' => 'date',
    ];
    public function interestedClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'interested_class_id');
    }
}
