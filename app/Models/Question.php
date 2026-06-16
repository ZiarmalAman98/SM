<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function language()
    {
        return $this->belongsTo(QuestionLanguage::class, 'language_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function difficulty()
    {
        return $this->belongsTo(QuestionDifficulty::class, 'difficulty_id');
    }

    public function choices()
    {
        return $this->hasMany(QuestionChoice::class, 'question_id');
    }


    protected static function boot()
    {
        parent::boot();


        static::creating(function ($question) {
            // If no password is provided, use the phone number as the password
            if (empty($question->user_id)) {
                $question->user_id =  auth()->id(); // Hash the phone number to use as the password
            }
        });
    }
}
