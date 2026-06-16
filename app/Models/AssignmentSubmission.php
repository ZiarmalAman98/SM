<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class AssignmentSubmission extends Model
{

    protected $fillable = [
        'assignment_id',
        'student_id',
        'file_path',
        'submitted_at',
        'status',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class)->withDefault([
            'title' => __('Deleted assignment'),
        ]);
    }

    /**
     * Get the student who submitted this assignment.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id')
            ->where('type', 'student')
            ->withDefault([
                'name' => __('Deleted student'),
                'father_name' => null,
            ]);
    }
    protected $casts = [
        'submitted_at' => 'datetime',
    ];
}
