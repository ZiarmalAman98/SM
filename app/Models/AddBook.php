<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Model;

class AddBook extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'author',
        'description',
        'file',
        'state',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function borrowRecords(): HasMany
    {
        return $this->hasMany(BorrowBook::class, 'addBook_id');
    }
}
