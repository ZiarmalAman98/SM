<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\FeeType;
use App\Models\ParentGuardian;
use App\Models\ParentInvoice;
use App\Models\ParentInvoiceItem;
use App\Models\ParentInvoicePayment;
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

class BasitGrade5FullSeeder extends Seeder
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
                ['email' => 'basit.grade5@school.test'],
                [
                    'name' => 'Basit',
                    'last_name' => 'Rahimi',
                    'father_name' => 'Abdul Hadi',
                    'password' => Hash::make('password'),
                    'type' => 'student',
                    'branch_id' => 1,
                    'active_status' => 1,
                ]
            );

            $student = Student::query()
                ->where('user_id', $studentUser->id)
                ->orWhere('admission_no', 'ADM-2026-006')
                ->orWhere('roll_no', 'R006')
                ->first();

            if ($student) {
                $student->fill([
                    'user_id' => $studentUser->id,
                    'grand_father_name' => 'Haji Karim',
                    'tazkira_number' => 'TZ-BASIT-006',
                    'admission_no' => 'ADM-2026-006',
                    'roll_no' => 'R006',
                    'dob' => '2015-09-10',
                    'gender' => 'Male',
                    'category_id' => $category->id,
                    'admission_date' => '2026-01-20',
                    'address' => 'Kabul, Afghanistan',
                    'section_id' => $section->id,
                    'phone' => '0771000111',
                ])->save();
            } else {
                $student = Student::create([
                    'user_id' => $studentUser->id,
                    'grand_father_name' => 'Haji Karim',
                    'tazkira_number' => 'TZ-BASIT-006',
                    'admission_no' => 'ADM-2026-006',
                    'roll_no' => 'R006',
                    'dob' => '2015-09-10',
                    'gender' => 'Male',
                    'category_id' => $category->id,
                    'admission_date' => '2026-01-20',
                    'address' => 'Kabul, Afghanistan',
                    'section_id' => $section->id,
                    'phone' => '0771000111',
                ]);
            }

            $studentClass = StudentClass::updateOrCreate(
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
                ['email' => 'guardian.basit.grade5@school.test'],
                [
                    'name' => 'Abdul Hadi',
                    'last_name' => 'Rahimi',
                    'father_name' => 'Sultan Mohammad',
                    'password' => Hash::make('password'),
                    'type' => 'guardian',
                    'branch_id' => 1,
                    'active_status' => 1,
                ]
            );

            $parentGuardian = ParentGuardian::query()
                ->where('user_id', $guardianUser->id)
                ->orWhere('family_code', 'FAM-000006')
                ->first();

            if ($parentGuardian) {
                $parentGuardian->fill([
                    'user_id' => $guardianUser->id,
                    'family_code' => 'FAM-000006',
                    'address' => 'Kabul, Afghanistan',
                ])->save();
            } else {
                $parentGuardian = ParentGuardian::create([
                    'user_id' => $guardianUser->id,
                    'family_code' => 'FAM-000006',
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
                'Tafsir Sharif' => ['mid' => 31, 'final' => 50],
                'Theology' => ['mid' => 29, 'final' => 52],
                'Pashto' => ['mid' => 34, 'final' => 53],
                'Dari' => ['mid' => 30, 'final' => 47],
                'English' => ['mid' => 28, 'final' => 49],
                'Math/Algebra' => ['mid' => 35, 'final' => 54],
                'Physics' => ['mid' => 27, 'final' => 45],
                'Chemistry' => ['mid' => 33, 'final' => 50],
                'Biology' => ['mid' => 32, 'final' => 51],
                'Geology' => ['mid' => 29, 'final' => 48],
                'History' => ['mid' => 26, 'final' => 44],
                'Geography' => ['mid' => 30, 'final' => 46],
                'Computer' => ['mid' => 36, 'final' => 56],
                'Sports' => ['mid' => 38, 'final' => 58],
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

            $registrationFee = FeeType::firstOrCreate(
                ['name' => 'Admission Fee'],
                ['default_amount' => 1500]
            );
            $monthlyFee = FeeType::firstOrCreate(
                ['name' => 'Monthly Tuition'],
                ['default_amount' => 3000]
            );
            $transportFee = FeeType::firstOrCreate(
                ['name' => 'Transport Fee'],
                ['default_amount' => 1200]
            );

            $invoice = ParentInvoice::updateOrCreate(
                ['invoice_number' => 'PINV-BASIT-202606'],
                [
                    'parent_guardian_id' => $parentGuardian->id,
                    'family_code' => 'FAM-000006',
                    'billing_month' => 'Jawza',
                    'billing_year' => 1405,
                    'invoice_date' => '2026-06-20',
                    'due_date' => '2026-06-25',
                    'previous_balance' => 0,
                    'subtotal' => 5700,
                    'total_amount' => 5700,
                    'paid_amount' => 5700,
                    'balance' => 0,
                    'status' => 'paid',
                    'notes' => 'Basit Grade 5 full seeded invoice.',
                ]
            );

            $items = [
                ['fee_type' => $registrationFee, 'description' => 'Admission Fee', 'amount' => 1500],
                ['fee_type' => $monthlyFee, 'description' => 'Monthly Tuition Fee', 'amount' => 3000],
                ['fee_type' => $transportFee, 'description' => 'Transport Fee', 'amount' => 1200],
            ];

            foreach ($items as $index => $item) {
                ParentInvoiceItem::updateOrCreate(
                    [
                        'parent_invoice_id' => $invoice->id,
                        'student_id' => $studentUser->id,
                        'fee_type_id' => $item['fee_type']->id,
                    ],
                    [
                        'student_class_id' => $studentClass->id,
                        'class_id' => $class->id,
                        'billing_month' => 'Jawza',
                        'billing_year' => 1405,
                        'description' => $item['description'],
                        'gross_amount' => $item['amount'],
                        'discount_amount' => 0,
                        'amount' => $item['amount'],
                        'is_previous_balance' => false,
                    ]
                );
            }

            ParentInvoicePayment::updateOrCreate(
                ['receipt_number' => 'PIP-BASIT-202606'],
                [
                    'parent_invoice_id' => $invoice->id,
                    'amount' => 5700,
                    'payment_date' => '2026-06-21',
                    'payment_method' => 'cash',
                    'reference_number' => 'BASIT-CASH-01',
                    'notes' => 'Full payment for Basit Grade 5 seeded invoice.',
                ]
            );

            $invoice->recalculatePayments();
        });
    }
}
