<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassAttendance extends Model
{
    protected $table = 'class_attendances';

    protected $fillable = [
        'student_id',
        'branch_id',
        'school_class_id',
        'date',
        'morning_status',
        'afternoon_status',
        'status',
        'is_leave',
        'is_sick',
        'leave_reason',
        'sick_reason',
        'leave_start_date',
        'leave_end_date',
        'leave_type',
    ];

    protected $casts = [
        'morning_status'   => 'boolean',
        'afternoon_status' => 'boolean',
        'date'             => 'date',
        'is_leave'         => 'boolean',
        'is_sick'          => 'boolean',
        'leave_start_date' => 'date',
        'leave_end_date'   => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id'); // 👈 important fix
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id'); // 👈 important fix
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * Get the attendance status with leave/sick information
     */
    public function getDetailedStatusAttribute()
    {
        if ($this->is_sick) {
            return 'sick';
        }
        if ($this->is_leave) {
            return 'leave';
        }
        return $this->status;
    }

    /**
     * Check if student is on leave for a specific date range
     */
    public function isOnLeave($date = null)
    {
        $checkDate = $date ?: $this->date;
        return $this->is_leave &&
               $this->leave_start_date &&
               $this->leave_end_date &&
               $checkDate >= $this->leave_start_date &&
               $checkDate <= $this->leave_end_date;
    }

    /**
     * Get leave type options
     */
    public static function getLeaveTypeOptions()
    {
        return [
            'personal' => __('Personal'),
            'family' => __('Family'),
            'emergency' => __('Emergency'),
            'other' => __('Other'),
        ];
    }
}
