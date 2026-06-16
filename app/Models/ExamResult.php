<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ExamResult extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'subject_id',
        'class_id',
        'marks',
        'written_marks',
        'recital_marks',
        'homework_marks',
        'class_activity_marks',
        'mark_in_words',
    ];

    /**
     * Get the exam this result belongs to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }


    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }


    /**
     * Get the student this result belongs to.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id')->where('type', 'student');
    }


    public function getGradeAttribute(): ?string
    {
        return GradeSystem::query()
            ->where('from', '<=', $this->marks)
            ->where('to', '>=', $this->marks)
            ->value('title');
    }
}
