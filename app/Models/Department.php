<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{

    use HasFactory;
    /**
     * Relationship with PlacementTest.
     * A department can have many placement tests.
     */
    public function placementTests()
    {
        return $this->hasMany(PlacementTest::class, 'department_id');
    }


    public function head()
    {
        return $this->belongsTo(User::class, 'head_id')
            ->whereRelation('roles', 'name', '!=', 'student'); // Use role_id to identify department heads
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }


    public function students()
    {
        return $this->hasMany(User::class, 'department_id')->whereRelation('roles', 'name', 'student');
    }
}
