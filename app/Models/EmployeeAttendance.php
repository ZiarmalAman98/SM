<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class EmployeeAttendance extends Model
    {
        protected $table = 'employee_attendance';

        // Mass assignable attributes
        protected $fillable = [
            'employee_id',
            'date',
            'status',
        ];

        // Relationship with Employee
        public function employee()
        {
            return $this->belongsTo(User::class, 'employee_id');
        }
    }
