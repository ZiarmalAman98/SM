<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeSystem extends Model
{
    protected $fillable = [
        'from',
        'to',
        'title',
    ];

    /**
     * Check if a mark is within this grade range.
     */
    public function matches(float $mark): bool
    {
        return $mark >= $this->from && $mark <= $this->to;
    }
}
