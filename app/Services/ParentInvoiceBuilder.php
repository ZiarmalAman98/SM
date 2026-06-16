<?php

namespace App\Services;

use App\Models\ParentGuardian;
use App\Models\ParentInvoice;
use App\Models\FeeGroupAssignment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Morilog\Jalali\Jalalian;

class ParentInvoiceBuilder
{
    public const MONTHS = [
        'Hamal' => 'Hamal',
        'Sawr' => 'Sawr',
        'Jawza' => 'Jawza',
        'Saratan' => 'Saratan',
        'Asad' => 'Asad',
        'Sunbula' => 'Sunbula',
        'Mizan' => 'Mizan',
        'Aqrab' => 'Aqrab',
        'Qaws' => 'Qaws',
        'Jadi' => 'Jadi',
        'Dalw' => 'Dalw',
        'Hoot' => 'Hoot',
    ];

    public function preview(?int $parentGuardianId, ?string $month, ?int $year): array
    {
        if (! $parentGuardianId || ! $month || ! $year) {
            return $this->emptyPreview();
        }

        $parentGuardian = ParentGuardian::with([
            'user',
            'linkedStudents.student.student',
            'linkedStudents.student.studentClasses.schoolClass',
            'linkedStudents.student.studentClasses.feeTypes',
        ])->find($parentGuardianId);

        if (! $parentGuardian) {
            return $this->emptyPreview();
        }

        $items = $this->studentFeeItems($parentGuardian, $month, $year);
        $previousBalance = $this->previousBalance($parentGuardian->id, $month, $year);

        if ($previousBalance > 0) {
            array_unshift($items, [
                'student_id' => null,
                'student_name' => 'Previous unpaid balance',
                'student_class_id' => null,
                'class_id' => null,
                'class_name' => '-',
                'fee_type_id' => null,
                'fee_group_assignment_id' => null,
                'fee_discount_id' => null,
                'fee_type_name' => 'Previous Balance',
                'billing_month' => $month,
                'billing_year' => $year,
                'description' => "Previous unpaid family balance before {$month} {$year}",
                'gross_amount' => $previousBalance,
                'discount_amount' => 0,
                'amount' => $previousBalance,
                'is_previous_balance' => true,
            ]);
        }

        $subtotal = collect($items)
            ->where('is_previous_balance', false)
            ->sum('amount');
        $total = $subtotal + $previousBalance;

        return [
            'family_code' => $parentGuardian->family_code,
            'items' => $items,
            'previous_balance' => $previousBalance,
            'subtotal' => $subtotal,
            'total_amount' => $total,
            'balance' => $total,
        ];
    }

    public function create(array $data): ParentInvoice
    {
        return DB::transaction(function () use ($data) {
            $exists = ParentInvoice::query()
                ->where('parent_guardian_id', $data['parent_guardian_id'])
                ->where('billing_year', $data['billing_year'])
                ->where('billing_month', $data['billing_month'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'billing_month' => __('An invoice already exists for this family, month, and year.'),
                ]);
            }

            $preview = $this->preview(
                (int) $data['parent_guardian_id'],
                $data['billing_month'],
                (int) $data['billing_year'],
            );

            if (empty($preview['items'])) {
                throw ValidationException::withMessages([
                    'parent_guardian_id' => __('No invoice lines were found. Link students and fee types first.'),
                ]);
            }

            $invoice = ParentInvoice::create([
                'invoice_number' => $data['invoice_number'] ?? null,
                'parent_guardian_id' => $data['parent_guardian_id'],
                'family_code' => $preview['family_code'],
                'billing_month' => $data['billing_month'],
                'billing_year' => $data['billing_year'],
                'invoice_date' => $data['invoice_date'],
                'due_date' => $data['due_date'] ?? null,
                'previous_balance' => $preview['previous_balance'],
                'subtotal' => $preview['subtotal'],
                'total_amount' => $preview['total_amount'],
                'paid_amount' => 0,
                'balance' => $preview['balance'],
                'status' => 'issued',
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($preview['items'] as $item) {
                $invoice->items()->create([
                    'student_id' => $item['student_id'],
                    'student_class_id' => $item['student_class_id'],
                    'class_id' => $item['class_id'],
                    'fee_type_id' => $item['fee_type_id'],
                    'fee_group_assignment_id' => $item['fee_group_assignment_id'] ?? null,
                    'fee_discount_id' => $item['fee_discount_id'] ?? null,
                    'billing_month' => $item['billing_month'],
                    'billing_year' => $item['billing_year'],
                    'description' => $item['description'],
                    'gross_amount' => $item['gross_amount'] ?? $item['amount'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'amount' => $item['amount'],
                    'is_previous_balance' => $item['is_previous_balance'],
                ]);
            }

            return $invoice;
        });
    }

    public function generateMonthly(array $data): array
    {
        $month = $data['billing_month'];
        $year = (int) $data['billing_year'];
        $invoiceDate = $data['invoice_date'] ?? now();
        $dueDate = $data['due_date'] ?? null;
        $notes = $data['notes'] ?? __('Automatically generated monthly invoice.');

        $summary = [
            'created' => 0,
            'skipped_existing' => 0,
            'skipped_empty' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        ParentGuardian::query()
            ->orderBy('id')
            ->chunkById(100, function ($parentGuardians) use ($month, $year, $invoiceDate, $dueDate, $notes, &$summary): void {
                foreach ($parentGuardians as $parentGuardian) {
                    $exists = ParentInvoice::query()
                        ->where('parent_guardian_id', $parentGuardian->id)
                        ->where('billing_year', $year)
                        ->where('billing_month', $month)
                        ->exists();

                    if ($exists) {
                        $summary['skipped_existing']++;
                        continue;
                    }

                    $preview = $this->preview($parentGuardian->id, $month, $year);

                    if (empty($preview['items'])) {
                        $summary['skipped_empty']++;
                        continue;
                    }

                    try {
                        $this->create([
                            'parent_guardian_id' => $parentGuardian->id,
                            'billing_month' => $month,
                            'billing_year' => $year,
                            'invoice_date' => $invoiceDate,
                            'due_date' => $dueDate,
                            'notes' => $notes,
                        ]);

                        $summary['created']++;
                    } catch (\Throwable $exception) {
                        $summary['failed']++;
                        $summary['errors'][] = "{$parentGuardian->family_code}: {$exception->getMessage()}";
                    }
                }
            });

        return $summary;
    }

    public static function currentBillingMonth(): string
    {
        $monthNumber = Jalalian::now()->getMonth();

        return array_keys(self::MONTHS)[$monthNumber - 1] ?? 'Hamal';
    }

    public static function billingPeriodForGeneration(?Jalalian $date = null): array
    {
        $date ??= Jalalian::now();

        if (self::isEndOfMonthGenerationWindow($date)) {
            $date = (new Jalalian($date->getYear(), $date->getMonth(), 1))->addMonths();
        }

        return [
            'month' => array_keys(self::MONTHS)[$date->getMonth() - 1] ?? 'Hamal',
            'year' => $date->getYear(),
        ];
    }

    public static function canRunAutomaticGeneration(?Jalalian $date = null): bool
    {
        $date ??= Jalalian::now();

        return $date->getDay() <= 2 || self::isEndOfMonthGenerationWindow($date);
    }

    private static function isEndOfMonthGenerationWindow(Jalalian $date): bool
    {
        return $date->getDay() >= 27;
    }

    private function studentFeeItems(ParentGuardian $parentGuardian, string $month, int $year): array
    {
        $items = [];

        foreach ($parentGuardian->linkedStudents as $link) {
            $student = $link->student;

            if (! $student || $student->type !== 'student') {
                continue;
            }

            $studentClass = $this->billableClass($student->id);

            if (! $studentClass) {
                continue;
            }

            array_push($items, ...$this->assignmentFeeItems($student, $studentClass, $month, $year));
        }

        return $items;
    }

    private function assignmentFeeItems(User $student, StudentClass $studentClass, string $month, int $year): array
    {
        $studentProfile = Student::where('user_id', $student->id)->first();
        $assignments = $this->billableAssignments($studentProfile?->id, $studentClass->class_id);
        $lines = [];

        foreach ($assignments as $assignment) {
            $discountRemaining = (float) ($assignment->feeDiscount?->discount_value ?? 0);

            foreach ($assignment->feeGroup?->feeGroupFeeTypes ?? [] as $groupFeeType) {
                $feeType = $groupFeeType->feeType;

                if (! $feeType) {
                    continue;
                }

                $grossAmount = (float) ($groupFeeType->amount ?? $feeType->default_amount ?? 0);

                if ($grossAmount <= 0) {
                    continue;
                }

                $discountAmount = min($grossAmount, max(0, $discountRemaining));
                $discountRemaining -= $discountAmount;
                $netAmount = max(0, $grossAmount - $discountAmount);

                if ($netAmount <= 0) {
                    continue;
                }

                $studentName = trim($student->name . ' ' . $student->last_name);
                $className = $studentClass->schoolClass?->class_name ?? '-';
                $discountText = $discountAmount > 0
                    ? ' after ' . number_format($discountAmount, 2) . ' AFN discount'
                    : '';

                $lines[$feeType->id] = [
                    'student_id' => $student->id,
                    'student_name' => $studentName,
                    'student_class_id' => $studentClass->id,
                    'class_id' => $studentClass->class_id,
                    'class_name' => $className,
                    'fee_type_id' => $feeType->id,
                    'fee_group_assignment_id' => $assignment->id,
                    'fee_discount_id' => $discountAmount > 0 ? $assignment->fee_discount_id : null,
                    'fee_type_name' => $feeType->name,
                    'billing_month' => $month,
                    'billing_year' => $year,
                    'description' => "{$feeType->name} for {$studentName} ({$className}) - {$month} {$year}{$discountText}",
                    'gross_amount' => $grossAmount,
                    'discount_amount' => $discountAmount,
                    'amount' => $netAmount,
                    'is_previous_balance' => false,
                ];
            }
        }

        return array_values($lines);
    }

    private function billableAssignments(?int $studentProfileId, int $classId)
    {
        $assignments = FeeGroupAssignment::with([
            'feeGroup.feeGroupFeeTypes.feeType',
            'feeDiscount',
        ])
            ->where(function ($query) use ($studentProfileId, $classId) {
                $query->where(function ($query) use ($classId) {
                    $query
                        ->where('assignable_type', SchoolClass::class)
                        ->where('assignable_id', $classId);
                });

                if ($studentProfileId) {
                    $query->orWhere(function ($query) use ($studentProfileId) {
                        $query
                            ->where('assignable_type', Student::class)
                            ->where('assignable_id', $studentProfileId);
                    });
                }
            })
            ->orderByRaw("assignable_type = ? asc", [Student::class])
            ->orderByDesc('effective_date')
            ->orderByDesc('id')
            ->get();

        return $assignments->unique(fn(FeeGroupAssignment $assignment) => $assignment->fee_group_id . ':' . $assignment->assignable_type);
    }

    private function billableClass(int $studentId): ?StudentClass
    {
        return StudentClass::with(['schoolClass'])
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->orderByDesc('academic_year')
            ->orderByDesc('id')
            ->first();
    }

    private function previousBalance(int $parentGuardianId, string $month, int $year): float
    {
        return (float) ParentInvoice::query()
            ->where('parent_guardian_id', $parentGuardianId)
            ->where('balance', '>', 0)
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->where(function ($query) use ($month, $year) {
                $query
                    ->where('billing_year', '!=', $year)
                    ->orWhere('billing_month', '!=', $month);
            })
            ->sum('balance');
    }

    private function emptyPreview(): array
    {
        return [
            'family_code' => null,
            'items' => [],
            'previous_balance' => 0,
            'subtotal' => 0,
            'total_amount' => 0,
            'balance' => 0,
        ];
    }
}
