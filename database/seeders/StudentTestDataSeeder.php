<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\StudentClass;
use Illuminate\Support\Facades\Hash;

class StudentTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['name' => 'احمد علی', 'father_name' => 'محمد علی'],
            ['name' => 'فاطمه حسن', 'father_name' => 'علی حسن'],
            ['name' => 'محمد رضا', 'father_name' => 'احمد رضا'],
            ['name' => 'عایشه خان', 'father_name' => 'عبدالله خان'],
            ['name' => 'حسن محمود', 'father_name' => 'یوسف محمود'],
            ['name' => 'زینب احمد', 'father_name' => 'سلیم احمد'],
            ['name' => 'عمر فاروق', 'father_name' => 'طارق فاروق'],
            ['name' => 'خدیجه علی', 'father_name' => 'حامد علی'],
            ['name' => 'یوسف حسین', 'father_name' => 'کریم حسین'],
            ['name' => 'مریم خان', 'father_name' => 'نعیم خان'],
        ];

        foreach ($students as $index => $studentData) {
            // Create user
            $user = User::create([
                'name' => $studentData['name'],
                'father_name' => $studentData['father_name'],
                'email' => 'teststudent' . ($index + 1) . time() . '@school.com',
                'password' => Hash::make('password'),
                'type' => 'student',
            ]);

            // Create student
            $student = Student::create([
                'user_id' => $user->id,
                'admission_no' => 'TST' . time() . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'roll_no' => 'R' . time() . str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'dob' => now()->subYears(rand(15, 18))->format('Y-m-d'),
                'gender' => $index % 2 == 0 ? 'Male' : 'Female',
                'category_id' => 1, // Assuming category exists
                'admission_date' => now()->subMonths(rand(1, 12)),
                'address' => 'کابل، افغانستان',
                'height' => rand(150, 180),
                'weight' => rand(45, 75),
            ]);

            // Assign to class 12
            StudentClass::create([
                'student_id' => $student->id,
                'class_id' => 12,
                'academic_year' => 2024,
                'status' => 'active',
            ]);
        }
    }
}