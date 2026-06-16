<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowBook extends Model
{
    protected $fillable = [
        'user_id',
        'addBook_id',
        'borrow_date',
        'return_date',
        'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => __('Deleted user'),
        ]);
    }

    public function addBook(): BelongsTo
    {
        return $this->belongsTo(AddBook::class, 'addBook_id')->withDefault([
            'title' => __('Deleted book'),
        ]);
    }

    protected $casts = [
        'borrow_date' => 'datetime',
        'return_date' => 'datetime',
    ];
}
