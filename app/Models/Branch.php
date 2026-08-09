<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_name',
        'branch_description',
        'branch_status',
        'branch_address',
        'branch_phone',
        'branch_email',
        'branch_website',
        'branch_manager_name',
        'monthly_budget',
    ];

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'branch_manager_name');
    }

    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
