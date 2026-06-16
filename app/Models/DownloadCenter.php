<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadCenter extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file',
        'type',
        'uploaded_by',
    ];

    /**
     * Relationship: DownloadCenter belongs to a User (Teacher or Principal)
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by')->withDefault([
            'name' => __('Unknown'),
        ]);
    }
}
