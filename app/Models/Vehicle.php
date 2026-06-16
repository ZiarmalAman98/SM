<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'vehicle_number',
        'model',
        'type',
        'capacity',
        'driver_name',
        'contact_number',
        'status',
    ];

    /**
     * Get the assignments for the vehicle.
     */
    public function vehicleAssignments(): HasMany
    {
        return $this->hasMany(VehicleAssignment::class);
    }
}
