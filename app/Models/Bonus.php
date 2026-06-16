<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->where('type', 'staff');
    }
}
   