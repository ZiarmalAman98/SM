<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationalLevel extends Model
{
    use HasFactory;



    public function students()
    {
        return $this->hasMany(User::class, 'educational_level_id')->whereRelation('roles', 'name', 'student');
    }
}
