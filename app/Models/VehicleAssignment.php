<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleAssignment extends Model
{
    protected $fillable = ['vehicle_id', 'route_id'];

    /**
     * Get the vehicle assigned to this assignment.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the route assigned to this assignment.
     */
    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Get all student transport reports for this assignment.
     */
    public function studentTransportReports(): HasMany
    {
        return $this->hasMany(StudentTransportReport::class);
    }
}
