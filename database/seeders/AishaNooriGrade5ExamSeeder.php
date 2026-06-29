<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ParentGuardian;
use App\Models\ParentStudent;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentCategory;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AishaNooriGrade5ExamSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $class = SchoolClass::firstOrCreate(
                ['class_name' => 'Grade 5'],
                ['description' => 'Primary class level five', 'teacher_id' => 2]
            );

            $section = Section::firstOrCreate(['name' => 'A']);
            $category = StudentCategory::firstOrCreate(['name' => 'Regular']);

            $studentUser = User::updateOrCreate(
                ['email' => 'aisha.noori.grade5@school.test'],
                [
                    'name' => 'Aisha',
                    'last_name' => 'Noori',
                    'father_name' => 'Ghulam Ali',
                    'password' => Hash::make('password'),
                    'type' => 'student',
                    'branch_id' => 1,
                    'active_status' => 1,
                ]
            );

            $student = Student::query()
                ->where('user_id', $studentUser->id)
                ->orWhere('admission_no', 'ADM-2026-004')
                ->orWhere('roll_no', 'R004')
                ->first();

            if ($student) {
                $student->fill([
                    'user_id' => $studentUser->id,
                    'grand_father_name' => 'Grand Father Name 4 for Students',
                    'tazkira_number' => 'Tazkira Number 4 for Students',
                    'admission_no' => 'ADM-2026-004',
                    'roll_no' => 'R004',
                    'dob' => '2026-06-14',
                    'gender' => 'Female',
                    'category_id' => $category->id,
                    'admission_date' => '2026-01-15',
                    'address' => 'Kabul, Afghanistan',
                    'section_id' => $section->id,
                    'phone' => '0785600566',
                ])->save();
            } else {
                $student = Student::create([
                    'user_id' => $studentUser->id,
                    'grand_father_name' => 'Grand Father Name 4 for Students',
                    'tazkira_number' => 'Tazkira Number 4 for Students',
                    'admission_no' => 'ADM-2026-004',
                    'roll_no' => 'R004',
                    'dob' => '2026-06-14',
                    'gender' => 'Female',
                    'category_id' => $category->id,
                    'admission_date' => '2026-01-15',
                    'address' => 'Kabul, Afghanistan',
                    'section_id' => $section->id,
                    'phone' => '0785600566',
                ]);
            }

            StudentClass::updateOrCreate(
                [
                    'student_id' => $student->user_id,
                    'class_id' => $class->id,
                ],
                [
                    'academic_year' => 2026,
                    'status' => 'active',
                ]
            );

            $guardianUser = User::updateOrCreate(
                ['email' => 'guardian.aisha.noori@school.test'],
                [
                    'name' => 'Ghulam Ali',
                    'last_name' => 'Noori',
                    'father_name' => 'Sayed Karim',
                    'password' => Hash::make('password'),
                    'type' => 'guardian',
                    'branch_id' => 1,
                    'active_status' => 1,
                ]
            );

            $parentGuardian = ParentGuardian::query()
                ->where('user_id', $guardianUser->id)
                ->orWhere('family_code', 'FAM-000004')
                ->first();

            if ($parentGuardian) {
                $parentGuardian->fill([
                    'user_id' => $guardianUser->id,
                    'family_code' => 'FAM-000004',
                    'address' => 'Kabul, Afghanistan',
                ])->save();
            } else {
                $parentGuardian = ParentGuardian::create([
                    'user_id' => $guardianUser->id,
                    'family_code' => 'FAM-000004',
                    'address' => 'Kabul, Afghanistan',
                ]);
            }

            ParentStudent::updateOrCreate(
                [
                    'student_id' => $studentUser->id,
                    'parent_guardian_id' => $guardianUser->id,
                ],
                ['relationship' => 'father']
            );

            $subjects = [
                'Tafsir Sharif',
                'Theology',
                'Pashto',
                'Dari',
                'English',
                'Math/Algebra',
                'Physics',
                'Chemistry',
                'Biology',
                'Geology',
                'History',
                'Geography',
                'Computer',
                'Sports',
                'Behavior',
            ];

            $subjectModels = collect($subjects)->mapWithKeys(function (string $name) use ($class) {
                $subject = Subject::updateOrCreate(
                    ['name' => $name, 'school_class_id' => $class->id],
                    ['teacher_id' => 2]
                );

                return [$name => $subject];
            });

            $midExam = Exam::updateOrCreate(
                ['name' => 'Grade 5 Mid-term Exam 2026', 'class_id' => $class->id, 'exam_type' => 'mid_term'],
                ['date' => '2026-06-13', 'time' => '09:00:00']
            );

            $finalExam = Exam::updateOrCreate(
                ['name' => 'Grade 5 Final Exam 2026', 'class_id' => $class->id, 'exam_type' => 'final'],
                ['date' => '2026-11-25', 'time' => '09:00:00']
            );

            $marks = [
                'Tafsir Sharif' => ['mid' => 24, 'final' => 56],
                'Theology' => ['mid' => 20, 'final' => 39],
                'Pashto' => ['mid' => 38, 'final' => 57],
                'Dari' => ['mid' => 26, 'final' => 10],
                'English' => ['mid' => 36, 'final' => 57],
                'Math/Algebra' => ['mid' => 10, 'final' => 51],
                'Physics' => ['mid' => 20, 'final' => 42],
                'Chemistry' => ['mid' => 40, 'final' => 42],
                'Biology' => ['mid' => 36, 'final' => 60],
                'Geology' => ['mid' => 32, 'final' => 48],
                'History' => ['mid' => 12, 'final' => 10],
                'Geography' => ['mid' => 20, 'final' => 48],
                'Computer' => ['mid' => 18, 'final' => 45],
                'Sports' => ['mid' => 40, 'final' => 60],
                'Behavior' => ['mid' => 40, 'final' => 60],
            ];

            foreach ($marks as $subjectName => $score) {
                $subject = $subjectModels[$subjectName];

                ExamResult::updateOrCreate(
                    [
                        'exam_id' => $midExam->id,
                        'student_id' => $studentUser->id,
                        'subject_id' => $subject->id,
                    ],
                    [
                        'class_id' => $class->id,
                        'marks' => $score['mid'],
                        'mark_in_words' => (string) $score['mid'],
                    ]
                );

                ExamResult::updateOrCreate(
                    [
                        'exam_id' => $finalExam->id,
                        'student_id' => $studentUser->id,
                        'subject_id' => $subject->id,
                    ],
                    [
                        'class_id' => $class->id,
                        'marks' => $score['final'],
                        'mark_in_words' => (string) $score['final'],
                    ]
                );
            }
        });
    }
}
