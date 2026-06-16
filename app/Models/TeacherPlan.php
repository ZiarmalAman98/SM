<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherPlan extends Model
{
    public function teacher()
    {
        return $this->belongsTo(\App\Models\User::class, 'teacher_id');
    }
    public function subject()
    {
        return $this->belongsTo(\App\Models\Subject::class);
    }
    public function schoolClass()
    {
        return $this->belongsTo(\App\Models\SchoolClass::class, 'class_id');
    }
}
