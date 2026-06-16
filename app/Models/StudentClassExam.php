<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentClassExam extends Model
{
    /**
     * Relationship with Student (User)
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relationship with SubjectClass
     */
    public function subjectClass()
    {
        return $this->belongsTo(Subject::class, 'subject_class_id');
    }

    /**
     * Relationship with ExamType
     */
    public function examType()
    {
        return $this->belongsTo(ExamType::class, 'exam_type_id');
    }
}
