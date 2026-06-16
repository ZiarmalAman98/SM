<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\StudentClass;
use App\Models\StudentCategory;
use App\Models\Section;
use Carbon\Carbon;

class CompleteSchoolSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // 1. Create basic data
            $basicData = $this->createBasicData();

            // 2. Create students with users
            $students = $this->createStudentsWithUsers($basicData['section']);

            // 3. Create subjects
            $subjects = $this->createSubjects($basicData['admin']);

            // 4. Create exams
            $exams = $this->createExams();

            // 5. Create exam results
            $this->createExamResults($students, $subjects, $exams);

            $this->command->info('✅ Complete school database seeded successfully!');
        });
    }

    private function createBasicData()
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'father_name' => 'Admin Father',
            'email' => 'admin@school.com',
            'password' => bcrypt('password'),
            'type' => 'admin',
        ]);

        // Create branch
        $branch = Branch::firstOrCreate(
            ['branch_name' => 'Main Branch'],
            [
                'branch_description' => 'Main school branch',
                'branch_status' => 'active',
                'branch_address' => 'Kabul, Afghanistan',
                'branch_phone' => '+93700000000',
                'branch_email' => 'main@school.com',
                'monthly_budget' => 50000.00,
            ]
        );

        // Create student category
        $category = StudentCategory::firstOrCreate(['name' => 'Regular']);

        // Create section
        $section = Section::firstOrCreate(['name' => 'A']);

        // Create classes
        $classes = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        foreach ($classes as $className) {
            SchoolClass::firstOrCreate([
                'class_name' => $className,
                'branch_id' => $branch->id,
            ]);
        }

        return ['section' => $section, 'category' => $category, 'admin' => $admin];
    }

    private function createStudentsWithUsers($section)
    {
        $students = [];
        $studentNames = [
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
            ['name' => 'نعیم احمد', 'father_name' => 'سید خان'],
            ['name' => 'ریحانه کریم', 'father_name' => 'عبدالرحیم'],
            ['name' => 'سلمان رضا', 'father_name' => 'عبدالرحمان'],
            ['name' => 'نسرین احمد', 'father_name' => 'کبیر احمد'],
            ['name' => 'جاوید قاسم', 'father_name' => 'سید قاسم'],
            ['name' => 'حمیده امین', 'father_name' => 'سید امین'],
            ['name' => 'بلال مالک', 'father_name' => 'عبدالمالک'],
            ['name' => 'سمیع احمد', 'father_name' => 'نور احمد'],
            ['name' => 'صفیه کریم', 'father_name' => 'کریم الله'],
            ['name' => 'فرید بصیر', 'father_name' => 'عبدالبصیر'],
        ];

        foreach ($studentNames as $i => $data) {
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'father_name' => $data['father_name'],
                'email' => 'student' . ($i + 1) . '@school.com',
                'password' => bcrypt('password'),
                'type' => 'student',
            ]);

            // Create student
            $student = Student::create([
                'grand_father_name' => 'عبدالرحمن',
                'tazkira_number' => 'TZ-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'ton_number' => 'TON-' . rand(100, 999),
                'admission_no' => 'ADM-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'roll_no' => $i + 1,
                'dob' => Carbon::now()->subYears(rand(15, 18))->format('Y-m-d'),
                'phone' => '07' . rand(70000000, 79999999),
                'gender' => $i % 2 === 0 ? 'Male' : 'Female',
                'category_id' => $section->id,
                'caste' => 'Default',
                'admission_date' => Carbon::now()->subYears(rand(0, 2))->format('Y-m-d'),
                'blood_group' => ['A+', 'B+', 'O+', 'AB+'][array_rand(['A+', 'B+', 'O+', 'AB+'])],
                'address' => 'کابل، افغانستان',
                'height' => rand(150, 180),
                'weight' => rand(45, 80),
                'user_id' => $user->id,
                'section_id' => $section->id,
            ]);

            // Assign to Grade 12
            $grade12 = SchoolClass::where('class_name', 'Grade 12')->first();
            StudentClass::create([
                'student_id' => $user->id,
                'class_id' => $grade12->id,
                'academic_year' => '2025',
            ]);

            $students[] = $student;
        }

        return collect($students);
    }

    private function createSubjects($admin)
    {
        $subjectNames = [
            'قرانکریم',
            'دنیات',
            'دری',
            'پښتو',
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

        $subjects = [];
        foreach ($subjectNames as $name) {
            $grade12 = SchoolClass::where('class_name', 'Grade 12')->first();
            $subject = Subject::create([
                'name' => $name,
                'school_class_id' => $grade12->id,
                'teacher_id' => $admin->id,
            ]);
            $subjects[] = $subject;
        }

        return collect($subjects);
    }

    private function createExams()
    {
        $exams = [
            'mid_term' => Exam::create([
                'name' => 'امتحان وسط سال 2025',
                'class_id' => SchoolClass::where('class_name', 'Grade 12')->first()->id,
                'exam_type' => 'mid_term',
                'date' => '2025-06-15',
                'time' => '09:00:00',
            ]),
            'final' => Exam::create([
                'name' => 'امتحان نهایی 2025',
                'class_id' => SchoolClass::where('class_name', 'Grade 12')->first()->id,
                'exam_type' => 'final',
                'date' => '2025-12-15',
                'time' => '09:00:00',
            ]),
        ];

        return $exams;
    }

    private function createExamResults($students, $subjects, $exams)
    {
        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                foreach ($exams as $type => $exam) {
                    ExamResult::create([
                        'student_id' => $student->user_id,
                        'exam_id' => $exam->id,
                        'subject_id' => $subject->id,
                        'class_id' => SchoolClass::where('class_name', 'Grade 12')->first()->id,
                        'marks' => $type === 'mid_term' ? rand(20, 40) : rand(40, 60),
                    ]);
                }
            }
        }
    }
}
