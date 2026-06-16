<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'name',
        'class_id',
        'exam_type',
        'date',
        'time',
    ];

    /**
     * Get the class this exam belongs to.
     */
    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Get all results for this exam.
     */
    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    public function getStatusAttribute(): string
    {
        return Carbon::parse($this->date)->isPast() ? 'Due' : 'Coming';
    }
}
