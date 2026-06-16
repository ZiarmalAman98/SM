<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Student;
use App\Models\ExamResult;
use App\Models\SchoolClass;
use Carbon\Carbon;

class Class12FullExamSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // 1️⃣ Define subjects (for Class 12)
            $subjects = [
                'قرانکریم',
                'دنیات',
                'دری',
                'پشتو',
                'لسان سوم',
                'انګلیسی',
                'ریاضی',
                'ساینس',
                'اجتماعیات',
                'خط/ رسم',
                'مهارت زندگی',
                'تربیت بدنی',
                'تهذیب',
            ];

            // 2️⃣ Get Class 12 record
            $class12 = SchoolClass::where('class_name', 'Grade 12')->first();
            if (!$class12) {
                $this->command->error('❌ Class 12 not found!');
                return;
            }

            // 3️⃣ Create or retrieve subjects
            $subjectModels = collect($subjects)->map(fn($name) =>
                Subject::firstOrCreate(
                    ['name' => $name, 'school_class_id' => $class12->id],
                    ['teacher_id' => 1]
                )
            );

            // 4️⃣ Create exams (midterm and final)
            $exams = [
                'mid_term' => Exam::firstOrCreate([
                    'name'      => 'امتحان وسط سال 2025',
                    'class_id'  => $class12->id,
                    'exam_type' => 'mid_term',
                    'date'      => '2025-06-15',
                    'time'      => '09:00:00',
                ]),
                'final' => Exam::firstOrCreate([
                    'name'      => 'امتحان نهایی 2025',
                    'class_id'  => $class12->id,
                    'exam_type' => 'final',
                    'date'      => '2025-12-15',
                    'time'      => '09:00:00',
                ]),
            ];

            // 5️⃣ Check or create Class 12 students
            $students = Student::whereHas('studentClasses', function($q) use ($class12) {
                $q->where('class_id', $class12->id);
            })->get();

            if ($students->isEmpty()) {
                $this->command->warn('⚠️ No students found in Class 12. Creating 10 sample students...');

                $sampleStudents = [
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
                    ['name' => 'نعیم', 'father_name' => 'سید خان'],
                ];

                foreach ($sampleStudents as $i => $data) {
                    Student::create([
                        'grand_father_name' => 'عبدالرحمن',
                        'tazkira_number' => 'TZ-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                        'ton_number' => 'TON-' . rand(100, 999),
                        'admission_no' => 'ADM-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                        'roll_no' => $i + 1,
                        'dob' => Carbon::now()->subYears(rand(17, 19))->subMonths(rand(1, 12))->format('Y-m-d'),
                        'phone' => '07' . rand(70000000, 79999999),
                        'gender' => $i % 2 === 0 ? 'Male' : 'Female',
                        'category_id' => 1,
                        'caste' => 'Default Caste',
                        'admission_date' => Carbon::now()->subYears(rand(0, 2))->format('Y-m-d'),
                        'photo_path' => null,
                        'blood_group' => ['A+', 'B+', 'O+', 'AB+'][array_rand(['A+', 'B+', 'O+', 'AB+'])],
                        'address' => 'کابل، افغانستان',
                        'height' => rand(150, 180),
                        'weight' => rand(45, 80),
                        'user_id' => 1,
                        'section_id' => 1,
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
                                'class_id' => $class12->id,
                                'marks'    => $type === 'mid_term' ? rand(20, 40) : rand(40, 60),
                            ]
                        );
                    }
                }
            }

            $this->command->info('✅ Class 12 students, subjects, exams, and marks created successfully!');
        });
    }
}
