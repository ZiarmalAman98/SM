<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ParentGuardian;
use App\Models\ParentInvoice;
use App\Models\ParentInvoicePayment;
use App\Models\ParentStudent;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentCategory;
use App\Models\StudentClass;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AmanDemoSetupSeeder extends Seeder
{
    /**
     * Create the minimum academic setup and linked accounts required to try every portal.
     * This seeder is idempotent: it can be run again without duplicating records.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $branch = Branch::firstOrCreate(
                ['branch_name' => 'Aman Main Campus'],
                [
                    'branch_status' => 'active',
                    'branch_address' => 'Kabul, Afghanistan',
                    'branch_phone' => '+93700000000',
                    'branch_email' => 'info@amanschool.edu.af',
                    'monthly_budget' => 0,
                ],
            );

            $category = StudentCategory::firstOrCreate(['name' => 'Regular']);
            $section = Section::firstOrCreate(['name' => 'A']);
            $password = Hash::make('Aman@2026');

            $teacherUser = User::updateOrCreate(
                ['email' => 'demo.teacher@amanschool.local'],
                [
                    'name' => 'Aman Demo Teacher',
                    'password' => $password,
                    'type' => 'teacher',
                    'branch_id' => $branch->id,
                    'active_status' => true,
                ],
            );

            Teacher::updateOrCreate(
                ['user_id' => $teacherUser->id],
                [
                    'designation' => 'Teacher',
                    'department' => 'General Education',
                    'gender' => 'Male',
                    'joining_date' => now()->toDateString(),
                    'basic_salary' => 0,
                ],
            );

            $class = SchoolClass::firstOrCreate(
                ['branch_id' => $branch->id, 'class_name' => 'Grade 1'],
                ['description' => 'Demo class', 'teacher_id' => $teacherUser->id],
            );

            $studentUser = User::updateOrCreate(
                ['email' => 'demo.student@amanschool.local'],
                [
                    'name' => 'Aman Demo Student',
                    'father_name' => 'Demo Guardian',
                    'password' => $password,
                    'type' => 'student',
                    'branch_id' => $branch->id,
                    'active_status' => true,
                ],
            );

            Student::updateOrCreate(
                ['user_id' => $studentUser->id],
                [
                    'admission_no' => 'AMAN-DEMO-001',
                    'roll_no' => 'DEMO-001',
                    'dob' => now()->subYears(10)->toDateString(),
                    'gender' => 'Male',
                    'category_id' => $category->id,
                    'section_id' => $section->id,
                    'admission_date' => now()->toDateString(),
                    'address' => 'Kabul, Afghanistan',
                ],
            );

            StudentClass::updateOrCreate(
                [
                    'student_id' => $studentUser->id,
                    'class_id' => $class->id,
                    'academic_year' => (int) now()->year,
                ],
                ['status' => 'active'],
            );

            $parentUser = User::updateOrCreate(
                ['email' => 'demo.parent@amanschool.local'],
                [
                    'name' => 'Aman Demo Parent',
                    'password' => $password,
                    'type' => 'guardian',
                    'branch_id' => $branch->id,
                    'active_status' => true,
                ],
            );

            ParentGuardian::updateOrCreate(
                ['user_id' => $parentUser->id],
                ['family_code' => 'FAM-DEMO-001', 'address' => 'Kabul, Afghanistan'],
            );

            ParentStudent::updateOrCreate(
                ['student_id' => $studentUser->id, 'parent_guardian_id' => $parentUser->id],
                ['relationship' => 'father'],
            );

            $incomeSourceId = $this->firstOrCreateLookup('income_sources', 'Monthly Fees');
            $expenseCategoryId = $this->firstOrCreateLookup('expense_categories', 'School Supplies');

            DB::table('incomes')->updateOrInsert(
                ['description' => 'Demo monthly tuition income'],
                [
                    'source_id' => $incomeSourceId,
                    'amount' => 15000,
                    'date' => now()->toDateString(),
                    'status' => 'approved',
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );

            DB::table('expenses')->updateOrInsert(
                ['description' => 'Demo school supplies expense'],
                [
                    'category_id' => $expenseCategoryId,
                    'amount' => 4500,
                    'date' => now()->toDateString(),
                    'status' => 'approved',
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );

            $guardian = ParentGuardian::where('user_id', $parentUser->id)->firstOrFail();
            $invoice = ParentInvoice::firstOrCreate(
                ['invoice_number' => 'PINV-DEMO-001'],
                [
                    'parent_guardian_id' => $guardian->id,
                    'family_code' => $guardian->family_code,
                    'billing_month' => now()->format('F'),
                    'billing_year' => (int) now()->year,
                    'invoice_date' => now()->toDateString(),
                    'due_date' => now()->addDays(10)->toDateString(),
                    'subtotal' => 3000,
                    'total_amount' => 3000,
                    'paid_amount' => 0,
                    'balance' => 3000,
                    'status' => 'issued',
                    'notes' => 'Demo student tuition invoice.',
                ],
            );

            ParentInvoicePayment::firstOrCreate(
                ['parent_invoice_id' => $invoice->id, 'reference_number' => 'DEMO-PAYMENT-001'],
                [
                    'amount' => 2000,
                    'payment_date' => now()->toDateString(),
                    'payment_method' => 'cash',
                    'notes' => 'Demo partial payment.',
                ],
            );

            User::updateOrCreate(
                ['email' => 'demo.admin@amanschool.local'],
                [
                    'name' => 'Aman Demo Admin',
                    'password' => $password,
                    'type' => 'admin',
                    'branch_id' => $branch->id,
                    'active_status' => true,
                ],
            );
        });
    }

    private function firstOrCreateLookup(string $table, string $name): int
    {
        $id = DB::table($table)->where('name', $name)->value('id');

        if ($id) {
            return (int) $id;
        }

        return (int) DB::table($table)->insertGetId([
            'name' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
