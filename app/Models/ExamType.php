<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    protected $fillable = ['name', 'max_source', 'min_source'];

    public function exams()
    {
        return $this->hasMany(StudentClassExam::class, 'exam_type_id');
    }
}
