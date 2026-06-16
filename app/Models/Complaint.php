<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    public function complainant()
    {
        return $this->hasOne(User::class, 'id', 'complainant_id');
    }

    /**
     * Get the correspondent user based on correspondent_type.
     */
    public function correspondent()
    {
        return $this->hasOne(User::class, 'id', 'correspondent_id');
    }
}
