<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Subject extends Model
{
    protected $fillable = [
        'name',
        'school_class_id',
        'teacher_id',
    ];

    /**
     * Get the class that this subject belongs to.
     */
    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * Get the teacher who teaches this subject.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class)->where('type', 'teacher');
    }

    /**
     * Get the schedules for this subject.
     */
    public function schedules()
    {
        return $this->hasMany(SubjectSchedule::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function exam_results()
    {
        return $this->hasMany(ExamResult::class);
    }
    public function examResults()
    {
        return $this->hasMany(ExamResult::class);
    }
}
