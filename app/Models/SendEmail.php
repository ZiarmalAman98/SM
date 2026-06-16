<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SendEmail extends Model
{
    protected $fillable = ['from', 'to', 'title', 'description'];
    protected $casts = [
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
        'attachments' => 'array',
        'is_urgent' => 'boolean',
        'read_receipt' => 'boolean',
    ];


}