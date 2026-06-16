<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppSetting extends Model
{
    use HasFactory;

    protected $table = 'app_settings';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'tab',
        'key',
        'default',
        'value',
    ];
}
