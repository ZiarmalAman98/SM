<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_id',
        'subject_id',
        'date',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id')->where('type', 'student');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id')->where('type', 'teacher');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
