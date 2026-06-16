<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ContactDetail extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => __('Deleted user'),
            'type' => null,
        ]);
    }
}
