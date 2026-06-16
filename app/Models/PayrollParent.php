<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollParent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'month',
        'year'
    ];

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}
