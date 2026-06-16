<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionDifficulty extends Model
{
    public function questions()
    {
        return $this->hasMany(Question::class, 'difficulty_id');
    }
}
