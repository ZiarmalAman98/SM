<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisciplineForm extends Model
{
    protected $fillable = [
        'student_id',
        'staff_id',
        'reason',
        'description',
        'status',
        'family_notified',
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relationship: A discipline form belongs to a student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relationship: A discipline form belongs to a staff member (e.g., Principal or Committee)
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    // // Optional: If you want to track warnings (if you created the `discipline_warnings` table)
    // public function warnings()
    // {
    //     return $this->hasMany(DisciplineWarning::class);
    // }

    // // Optional: If you created a suspensions table
    // public function suspension()
    // {
    //     return $this->hasOne(Suspension::class);
    // }
}
