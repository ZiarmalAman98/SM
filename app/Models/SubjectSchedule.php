<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectSchedule extends Model
{
    protected $fillable = [
        'subject_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    /**
     * Get the subject that this schedule belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
