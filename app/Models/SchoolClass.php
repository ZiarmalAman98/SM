<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolClass extends Model
{
    use HasFactory;

    /* readable label for dropdowns */
    public function getNameAttribute(): string
    {
        return $this->class_name ?: ('Class #' . $this->id);
    }

    public function teacher()
    {
        return $this->belongsTo(\App\Models\User::class, 'teacher_id');
    }



    protected $fillable = ['branch_id', 'teacher_id', 'class_name', 'description'];

    public function feeGroupAssignments(): MorphMany
    {
        return $this->morphMany(FeeGroupAssignment::class, 'assignable');
    }

    public function studentClasses(): HasMany
    {
        return $this->hasMany(StudentClass::class, 'class_id');
    }



    public function feePayments()
    {
        return $this->hasMany(FeePayment::class, 'class_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
