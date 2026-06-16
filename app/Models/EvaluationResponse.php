<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluationResponse extends Model
{
    protected $fillable = [
        'teacher_id',
        'student_id',
        'evaluation_question_id',
        'rating',
        'comment',
        'academic_year',
    ];

    /**
     * The teacher being evaluated.
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class)->where('type', 'teacher');
    }

    /**
     * The student who submitted the evaluation (nullable).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class)->where('type', 'student');
    }

    /**
     * The evaluation question this response is for.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(EvaluationQuestion::class, 'evaluation_question_id');
    }
}
