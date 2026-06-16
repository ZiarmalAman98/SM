<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeave extends Model
{

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')
            ->where('type', 'staff');

    }


    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    


}
