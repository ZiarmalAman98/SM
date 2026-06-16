<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ParentGuardian extends Model
{
    protected $fillable = [
        'user_id',
        'family_code',
        'address',
    ];

    protected static function booted(): void
    {
        static::creating(function (ParentGuardian $parentGuardian): void {
            if (blank($parentGuardian->family_code)) {
                $parentGuardian->family_code = static::generateFamilyCode();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(ParentInvoice::class);
    }

    public function linkedStudents(): HasMany
    {
        return $this->hasMany(ParentStudent::class, 'parent_guardian_id', 'user_id');
    }

    public static function generateFamilyCode(): string
    {
        $nextId = ((int) DB::table('parent_guardians')->max('id')) + 1;

        do {
            $code = 'FAM-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
            $nextId++;
        } while (static::where('family_code', $code)->exists());

        return $code;
    }
}
