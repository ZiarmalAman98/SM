<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    
    public function payrollParent()
    {
        return $this->belongsTo(PayrollParent::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id')
            ->where('type', 'staff')
            ->orWhere('type', 'teacher');
           
    }

    public function payment()
    {
        return $this->hasMany(Payment::class, 'payroll_id');
    }

    protected static function booted()
    {
        static::creating(function ($payroll) {
            $payroll->payroll_number = self::generatePayrollNumber();
        });
    }


    public static function generatePayrollNumber(): string
    {
        $yearMonth = now()->format('Ym'); // e.g., 202403 for March 2024

        // Extract the maximum payroll number directly for improved reliability
        $lastPayrollNumber = self::where('payroll_number', 'like', "PAY-{$yearMonth}-%")
            ->max('payroll_number');

        // Extract the sequence number from the last payroll number (if found)
        $nextNumber = $lastPayrollNumber
            ? (int) substr($lastPayrollNumber, -3) + 1
            : 1;

        // Return the correctly formatted payroll number
        return 'PAY-' . $yearMonth . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

}
