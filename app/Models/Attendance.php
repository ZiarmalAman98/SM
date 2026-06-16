<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_id',
        'school_class_id',
        'date',
        'morning_status',
        'afternoon_status',
    ];

    // NEW — make them proper bools on read / write
    protected $casts = [
        'morning_status'   => 'boolean',
        'afternoon_status' => 'boolean',

        'status' => 'integer',
        'date' => 'date',
    ];


    public function student()
    {
        return $this->belongsTo(User::class, 'student_id')->where('type', 'student');
    }

    /**
     * Get the teacher that owns the attendance.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id')->where('type', 'teacher');
    }

    /**
     * Get the subject associated with the attendance.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}