<?php

namespace Database\Seeders;

use App\Models\ParentGuardian;
use App\Models\ParentInvoice;
use App\Models\SchoolClass;
use App\Models\StudentClass;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class FinancialSummaryDemoSeeder extends Seeder
{
    public function run(): void
    {
        $class = SchoolClass::query()
            ->where('branch_id', 2)
            ->where('class_name', 'Grade 2')
            ->first();

        if (! $class) {
            return;
        }

        $parents = ParentGuardian::query()
            ->orderBy('id')
            ->limit(5)
            ->get();

        if ($parents->isEmpty()) {
            return;
        }

        $studentClasses = StudentClass::query()
            ->where('class_id', $class->id)
            ->where('status', 'active')
            ->limit(2)
            ->get();

        $studentClasses = $studentClasses->isNotEmpty()
            ? $studentClasses
            : StudentClass::query()->limit(2)->get();

        $months = [
            'Hamal',
            'Sawr',
            'Jawza',
            'Saratan',
            'Asad',
            'Sunbula',
            'Mizan',
            'Aqrab',
            'Qaws',
            'Jadi',
            'Dalw',
            'Hoot',
        ];

        $year = Jalalian::now()->getYear();
        $created = 0;

        DB::transaction(function () use ($parents, $studentClasses, $months, $year, &$created): void {
            foreach ($parents as $parent) {
                foreach ($months as $index => $month) {
                    if ($created >= 20) {
                        return;
                    }

                    $exists = ParentInvoice::query()
                        ->where('parent_guardian_id', $parent->id)
                        ->where('billing_year', $year)
                        ->where('billing_month', $month)
                        ->exists();

                    if ($exists) {
                        continue;
                    }

                    $amount = 1200 + ($created * 75);
                    $paid = $created % 3 === 0 ? $amount : round($amount * 0.65, 2);
                    $studentClass = $studentClasses[$created % max(1, $studentClasses->count())] ?? null;

                    $invoice = ParentInvoice::create([
                        'parent_guardian_id' => $parent->id,
                        'family_code' => $parent->family_code,
                        'billing_month' => $month,
                        'billing_year' => $year,
                        'invoice_date' => now()->subDays($created % 20),
                        'due_date' => now()->addDays(7),
                        'previous_balance' => 0,
                        'subtotal' => $amount,
                        'total_amount' => $amount,
                        'paid_amount' => 0,
                        'balance' => $amount,
                        'status' => 'issued',
                        'notes' => 'Financial summary demo data.',
                    ]);

                    $invoice->items()->create([
                        'student_id' => $studentClass?->student_id,
                        'student_class_id' => $studentClass?->id,
                        'class_id' => $studentClass?->class_id,
                        'billing_month' => $month,
                        'billing_year' => $year,
                        'description' => "Grade 2 monthly fee - {$month} {$year}",
                        'gross_amount' => $amount,
                        'discount_amount' => 0,
                        'amount' => $amount,
                        'is_previous_balance' => false,
                    ]);

                    $invoice->payments()->create([
                        'amount' => $paid,
                        'payment_date' => now()->subDays($created % 20),
                        'payment_method' => $created % 2 === 0 ? 'cash' : 'bank',
                        'reference_number' => 'FSR-DEMO-' . str_pad((string) ($created + 1), 3, '0', STR_PAD_LEFT),
                        'notes' => 'Financial summary demo payment.',
                    ]);

                    $created++;
                }
            }
        });

        $this->command?->info("Financial summary demo records created: {$created}");
    }
}
