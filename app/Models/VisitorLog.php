<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VisitorLog extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'visitor_name',
        'phone_number',
        'email',
        'entry_time',
        'exit_time',
        'person_to_meet',
        'purpose',
        'notes',
    ];

    public function personToMeet(): BelongsTo
    {
        return $this->belongsTo(User::class, 'person_to_meet');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('visitor_photos')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public');
    }
}
