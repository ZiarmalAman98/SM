<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTransportReport extends Model
{
    protected $fillable = [
        'user_id',
        'vehicle_assignment_id',
        'date_of_report',
        'remarks'
    ];

    /**
     * Get the student related to this report.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class)->where('type', 'student');
    }

    /**
     * Get the vehicle assignment related to this report.
     */
    public function vehicleAssignment(): BelongsTo
    {
        return $this->belongsTo(VehicleAssignment::class);
    }
}
