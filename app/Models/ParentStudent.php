<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentStudent extends Model
{
    protected $table = 'parent_student';

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function parentGuard()
    {
        return $this->belongsTo(User::class, 'parent_guardian_id');
    }
}
