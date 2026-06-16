<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

class StudentClass extends Model
{



    protected $fillable = [
        'student_id',
        'class_id',
        'status',
        'academic_year', // if you have this
    ];


    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id')
                    ->where('type', 'student');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function feeTypes(): BelongsToMany
    {
        return $this->belongsToMany(FeeType::class, 'student_class_fee_type')
            ->withTimestamps();
    }


    protected static function booted()
    {
        static::saving(function ($model) {
            if (blank($model->status)) {
                $model->status = 'active';
            }
        });

        static::saved(function ($model) {
            if ($model->status !== 'active') {
                return;
            }

            // Keep one active class per student, but allow completed/transferred records to stay as selected.
            StudentClass::where('student_id', $model->student_id)
                ->where('status', 'active')
                ->where('id', '!=', $model->id)
                ->update(['status' => 'completed']);
        });
    }
}
