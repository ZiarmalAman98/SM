<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Student;
use App\Models\User;
use App\Models\ExamResult;
use App\Models\SchoolClass;
use App\Models\StudentClass;
use Carbon\Carbon;

class Class5FullExamSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // 1️⃣ Define subjects (for Class 5)
            $subjects = [
                'قرانکریم',
                'دری',
                'پشتو',
                'انګلیسی',
                'ریاضی',
                'ساینس',
                'اجتماعیات',
                'خط/ رسم',
                'تربیت بدنی',
                'تهذیب',
            ];

            // 2️⃣ Get Class 5 record
            $class5 = SchoolClass::where('class_name', 'Grade 5')->first();
            if (!$class5) {
                $this->command->error('❌ Class 5 not found!');
                return;
            }

            // 3️⃣ Create or retrieve subjects
            $subjectModels = collect($subjects)->map(fn($name) =>
                Subject::firstOrCreate(
                    ['name' => $name, 'school_class_id' => $class5->id],
                    ['teacher_id' => 1]
                )
            );

            // 4️⃣ Create exams (midterm and final)
            $exams = [
                'mid_term' => Exam::firstOrCreate([
                    'name'      => 'امتحان وسط سال 2025',
                    'class_id'  => $class5->id,
                    'exam_type' => 'mid_term',
                    'date'      => '2025-06-10',
                    'time'      => '09:00:00',
                ]),
                'final' => Exam::firstOrCreate([
                    'name'      => 'امتحان نهایی 2025',
                    'class_id'  => $class5->id,
                    'exam_type' => 'final',
                    'date'      => '2025-12-10',
                    'time'      => '09:00:00',
                ]),
            ];

            // 5️⃣ Check or create Class 5 students
            $students = Student::whereHas('studentClasses', function($q) use ($class5) {
                $q->where('class_id', $class5->id);
            })->get();

            if ($students->isEmpty()) {
                $this->command->warn('⚠️ No students found in Class 5. Creating 15 sample students...');

                $sampleStudents = [
                    ['name' => 'احمد', 'father_name' => 'محمد'],
                    ['name' => 'سارا', 'father_name' => 'احمد'],
                    ['name' => 'عمر', 'father_name' => 'کریم'],
                    ['name' => 'زهرا', 'father_name' => 'عبدالله'],
                    ['name' => 'یاسین', 'father_name' => 'حسن'],
                    ['name' => 'مینا', 'father_name' => 'سعید'],
                    ['name' => 'رحمان', 'father_name' => 'کبیر'],
                    ['name' => 'صفیه', 'father_name' => 'رحیم'],
                    ['name' => 'کمال', 'father_name' => 'احمد'],
                    ['name' => 'ریحانه', 'father_name' => 'عبدالرحمان'],
                    ['name' => 'صابر', 'father_name' => 'محمود'],
                    ['name' => 'مروه', 'father_name' => 'نور احمد'],
                    ['name' => 'بلال', 'father_name' => 'یوسف'],
                    ['name' => 'نسرین', 'father_name' => 'کریم'],
                    ['name' => 'احسان', 'father_name' => 'عبدالمالک'],
                ];

                foreach ($sampleStudents as $i => $data) {
                    // Create User first
                    $user = User::create([
                        'name' => $data['name'],
                        'father_name' => $data['father_name'],
                        'email' => 'student' . ($i + 1) . '@class5.com',
                        'password' => bcrypt('password'),
                        'type' => 'student',
                    ]);
                    
                    // Create Student
                    $student = Student::create([
                        'grand_father_name' => 'عبدالرحمن',
                        'tazkira_number' => 'TZ5-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                        'ton_number' => 'TON5-' . rand(100, 999),
                        'admission_no' => 'ADM5-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                        'roll_no' => $i + 1,
                        'dob' => Carbon::now()->subYears(rand(10, 12))->subMonths(rand(1, 12))->format('Y-m-d'),
                        'phone' => '07' . rand(70000000, 79999999),
                        'gender' => $i % 2 === 0 ? 'Male' : 'Female',
                        'category_id' => 1,
                        'caste' => 'Default Caste',
                        'admission_date' => Carbon::now()->subYears(rand(0, 1))->format('Y-m-d'),
                        'photo_path' => null,
                        'blood_group' => ['A+', 'B+', 'O+', 'AB+'][array_rand(['A+', 'B+', 'O+', 'AB+'])],
                        'address' => 'کابل، افغانستان',
                        'height' => rand(120, 140),
                        'weight' => rand(25, 40),
                        'user_id' => $user->id,
                        'section_id' => 1,
                    ]);
                    
                    // Link student to class
                    StudentClass::create([
                        'student_id' => $student->id,
                        'class_id' => $class5->id,
                        'academic_year' => '2025'
                    ]);
                }

                $students = Student::all();
            }

            // 6️⃣ Create exam results
            foreach ($students as $student) {
                foreach ($subjectModels as $subject) {
                    foreach ($exams as $type => $exam) {
                        ExamResult::updateOrCreate(
                            [
                                'student_id' => $student->user_id,
                                'exam_id'    => $exam->id,
                                'subject_id' => $subject->id,
                            ],
                            [
                                'class_id' => $class5->id,
                                'marks'    => $type === 'mid_term' ? rand(20, 40) : rand(40, 60),
                            ]
                        );
                    }
                }
            }

            $this->command->info('✅ Class 5 students, subjects, exams, and marks created successfully!');
        });
    }
}
