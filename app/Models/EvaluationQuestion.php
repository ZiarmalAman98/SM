<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationQuestion extends Model
{
    protected $fillable = ['question_text', 'is_active'];

    /**
     * Responses for this question.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(EvaluationResponse::class, 'evaluation_question_id');
    }
}
