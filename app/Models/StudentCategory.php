<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentCategory extends Model
{
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }
}
