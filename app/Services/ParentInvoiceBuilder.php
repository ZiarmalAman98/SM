<?php

namespace App\Services;

use App\Models\ParentGuardian;
use App\Models\ParentInvoice;
use App\Models\InventorySale;
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

    public function preview(?int $parentGuardianId, ?string $month, ?int $year, ?array $inventorySaleIds = null): array
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

        $saleIds = $inventorySaleIds ?? $this->unpaidInventorySaleIds($parentGuardian->id);
        $items = $this->studentFeeItems($parentGuardian, $month, $year);
        $items = array_merge($items, $this->inventorySaleItems($parentGuardian->id, $month, $year, $saleIds));
        $previousBalanceDetails = $this->previousBalanceDetails($parentGuardian->id, $month, $year);
        $previousBalance = $previousBalanceDetails['amount'];

        if ($previousBalance > 0) {
            array_unshift($items, ...array_reverse($previousBalanceDetails['items']));
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

            $inventorySaleIds = array_key_exists('inventory_sale_ids', $data)
                ? array_map('intval', $data['inventory_sale_ids'] ?? [])
                : null;

            $preview = $this->preview(
                (int) $data['parent_guardian_id'],
                $data['billing_month'],
                (int) $data['billing_year'],
                $inventorySaleIds,
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
                    'inventory_sale_id' => $item['inventory_sale_id'] ?? null,
                    'billing_month' => $item['billing_month'],
                    'billing_year' => $item['billing_year'],
                    'description' => $item['description'],
                    'gross_amount' => $item['gross_amount'] ?? $item['amount'],
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'amount' => $item['amount'],
                    'is_previous_balance' => $item['is_previous_balance'],
                ]);
            }

            $this->attachInventorySalesToInvoice($invoice, $preview['items']);

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
            'updated' => 0,
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
                        ->where('status', '!=', 'cancelled')
                        ->exists();

                    if ($exists) {
                        try {
                            if ($this->attachUnpaidSalesToExistingInvoice($parentGuardian->id, $month, $year) > 0) {
                                $summary['updated']++;
                            } else {
                                $summary['skipped_existing']++;
                            }
                        } catch (\Throwable $exception) {
                            $summary['failed']++;
                            $summary['errors'][] = "{$parentGuardian->family_code}: {$exception->getMessage()}";
                        }

                        continue;
                    }

                    $saleIds = $this->unpaidInventorySaleIds($parentGuardian->id);
                    $preview = $this->preview($parentGuardian->id, $month, $year, $saleIds);

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
                            'inventory_sale_ids' => $saleIds,
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

        if (! empty($lines)) {
            return array_values($lines);
        }

        return $this->studentClassFeeItems($student, $studentClass, $month, $year);
    }

    private function studentClassFeeItems(User $student, StudentClass $studentClass, string $month, int $year): array
    {
        return $studentClass->feeTypes
            ->map(function ($feeType) use ($student, $studentClass, $month, $year): ?array {
                $grossAmount = (float) ($feeType->default_amount ?? 0);

                if ($grossAmount <= 0) {
                    return null;
                }

                $studentName = trim($student->name . ' ' . $student->last_name);
                $className = $studentClass->schoolClass?->class_name ?? '-';

                return [
                    'student_id' => $student->id,
                    'student_name' => $studentName,
                    'student_class_id' => $studentClass->id,
                    'class_id' => $studentClass->class_id,
                    'class_name' => $className,
                    'fee_type_id' => $feeType->id,
                    'fee_group_assignment_id' => null,
                    'fee_discount_id' => null,
                    'fee_type_name' => $feeType->name,
                    'billing_month' => $month,
                    'billing_year' => $year,
                    'description' => "{$feeType->name} for {$studentName} ({$className}) - {$month} {$year}",
                    'gross_amount' => $grossAmount,
                    'discount_amount' => 0,
                    'amount' => $grossAmount,
                    'is_previous_balance' => false,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function attachUnpaidSalesToExistingInvoice(int $parentGuardianId, string $month, int $year): int
    {
        $invoice = ParentInvoice::query()
            ->where('parent_guardian_id', $parentGuardianId)
            ->where('billing_year', $year)
            ->where('billing_month', $month)
            ->where('status', '!=', 'cancelled')
            ->first();

        if (! $invoice) {
            return 0;
        }

        $saleIds = $this->unpaidInventorySaleIds($parentGuardianId);

        if (empty($saleIds)) {
            return 0;
        }

        $items = $this->inventorySaleItems($parentGuardianId, $month, $year, $saleIds);

        foreach ($items as $item) {
            $invoice->items()->updateOrCreate(
                ['inventory_sale_id' => $item['inventory_sale_id']],
                [
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
                    'is_previous_balance' => false,
                ],
            );
        }

        $this->attachInventorySalesToInvoice($invoice, $items);
        $this->recalculateInvoiceTotals($invoice->fresh(['items', 'payments']));

        return count($items);
    }

    private function attachInventorySalesToInvoice(ParentInvoice $invoice, array $items): void
    {
        $saleIds = collect($items)
            ->pluck('inventory_sale_id')
            ->filter()
            ->map(fn($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($saleIds)) {
            return;
        }

        InventorySale::query()
            ->whereIn('id', $saleIds)
            ->where('parent_guardian_id', $invoice->parent_guardian_id)
            ->whereNull('parent_invoice_id')
            ->where('status', '!=', 'cancelled')
            ->update([
                'parent_invoice_id' => $invoice->id,
                'payment_destination' => 'parent_invoice',
            ]);
    }

    private function unpaidInventorySaleIds(int $parentGuardianId): array
    {
        return InventorySale::query()
            ->where('parent_guardian_id', $parentGuardianId)
            ->whereNull('parent_invoice_id')
            ->where('status', '!=', 'cancelled')
            ->where('balance', '>', 0)
            ->where(function ($query): void {
                $query->where('payment_destination', 'unpaid')
                    ->orWhere('sale_type', 'admission');
            })
            ->orderBy('sale_date')
            ->orderBy('id')
            ->pluck('id')
            ->map(fn($id): int => (int) $id)
            ->all();
    }

    private function recalculateInvoiceTotals(ParentInvoice $invoice): void
    {
        $previousBalance = (float) $invoice->items
            ->where('is_previous_balance', true)
            ->sum('amount');
        $subtotal = (float) $invoice->items
            ->where('is_previous_balance', false)
            ->sum('amount');
        $total = $previousBalance + $subtotal;
        $paid = (float) $invoice->payments->sum('amount');
        $balance = max(0, $total - $paid);

        $invoice->forceFill([
            'previous_balance' => $previousBalance,
            'subtotal' => $subtotal,
            'total_amount' => $total,
            'paid_amount' => $paid,
            'balance' => $balance,
            'status' => match (true) {
                $invoice->status === 'cancelled' => 'cancelled',
                $paid <= 0 => 'issued',
                $balance <= 0 => 'paid',
                default => 'partial',
            },
        ])->saveQuietly();
    }

    private function inventorySaleItems(int $parentGuardianId, string $month, int $year, array $inventorySaleIds): array
    {
        $inventorySaleIds = array_values(array_unique(array_filter(
            array_map('intval', $inventorySaleIds),
            fn(int $id): bool => $id > 0,
        )));

        if (empty($inventorySaleIds)) {
            return [];
        }

        return InventorySale::query()
            ->with(['items.product', 'student'])
            ->where('parent_guardian_id', $parentGuardianId)
            ->whereIn('id', $inventorySaleIds)
            ->whereNull('parent_invoice_id')
            ->where('status', '!=', 'cancelled')
            ->where('balance', '>', 0)
            ->where(function ($query): void {
                $query->where('payment_destination', 'unpaid')
                    ->orWhere('sale_type', 'admission');
            })
            ->orderBy('sale_date')
            ->orderBy('id')
            ->get()
            ->map(function (InventorySale $sale) use ($month, $year): array {
                $studentName = trim(($sale->student?->name ?? '') . ' ' . ($sale->student?->last_name ?? ''));
                $itemSummary = $sale->items
                    ->map(fn($item) => ($item->product?->name ?? __('Product')) . ' x ' . number_format((float) $item->quantity, 2))
                    ->implode(', ');
                $description = __('Inventory Sale :sale_no', ['sale_no' => $sale->sale_no]);

                return [
                    'student_id' => $sale->student_id,
                    'student_name' => $studentName !== '' ? $studentName : '-',
                    'student_class_id' => null,
                    'class_id' => null,
                    'class_name' => '-',
                    'fee_type_id' => null,
                    'fee_group_assignment_id' => null,
                    'fee_discount_id' => null,
                    'inventory_sale_id' => $sale->id,
                    'fee_type_name' => __('Inventory Sale'),
                    'billing_month' => $month,
                    'billing_year' => $year,
                    'description' => trim($description . ($itemSummary !== '' ? ": {$itemSummary}" : '')),
                    'gross_amount' => (float) $sale->balance,
                    'discount_amount' => 0,
                    'amount' => (float) $sale->balance,
                    'is_previous_balance' => false,
                ];
            })
            ->all();
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
        return StudentClass::with(['schoolClass', 'feeTypes'])
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->orderByDesc('academic_year')
            ->orderByDesc('id')
            ->first();
    }

    private function previousBalanceDetails(int $parentGuardianId, string $month, int $year): array
    {
        $targetMonthNumber = $this->billingMonthNumber($month);

        $priorInvoices = ParentInvoice::query()
            ->where('parent_guardian_id', $parentGuardianId)
            ->where('status', '!=', 'cancelled')
            ->withSum('payments as payments_paid_amount', 'amount')
            ->with(['items.student', 'items.schoolClass', 'items.feeType'])
            ->get(['id', 'billing_month', 'billing_year', 'subtotal'])
            ->filter(function (ParentInvoice $invoice) use ($targetMonthNumber, $year): bool {
                $invoiceMonthNumber = $this->billingMonthNumber($invoice->billing_month);

                return (int) $invoice->billing_year === $year
                    && $invoiceMonthNumber < $targetMonthNumber;
            })
            ->sortBy(fn(ParentInvoice $invoice): string => sprintf(
                '%04d%02d%010d',
                (int) $invoice->billing_year,
                $this->billingMonthNumber($invoice->billing_month),
                (int) $invoice->id,
            ))
            ->values();

        $priorMonthlyFees = (float) $priorInvoices->sum('subtotal');
        $remainingPayments = (float) $priorInvoices->sum('payments_paid_amount');
        $unpaidPeriods = [];
        $items = [];

        foreach ($priorInvoices as $invoice) {
            foreach ($invoice->items->where('is_previous_balance', false)->sortBy('id') as $item) {
                $lineAmount = (float) $item->amount;
                $paidForLine = min($remainingPayments, $lineAmount);
                $remainingPayments -= $paidForLine;
                $lineBalance = max(0, $lineAmount - $paidForLine);

                if ($lineBalance <= 0) {
                    continue;
                }

                $unpaidPeriods[] = "{$invoice->billing_month} {$invoice->billing_year}";

                $studentName = trim(($item->student?->name ?? '') . ' ' . ($item->student?->last_name ?? ''));
                $feeTypeName = $item->feeType?->name
                    ?? ($item->inventory_sale_id ? 'Inventory Sale' : 'Previous Balance');

                $items[] = [
                    'student_id' => $item->student_id,
                    'student_name' => $studentName !== '' ? $studentName : 'Previous unpaid balance',
                    'student_class_id' => $item->student_class_id,
                    'class_id' => $item->class_id,
                    'class_name' => $item->schoolClass?->class_name ?? '-',
                    'fee_type_id' => $item->fee_type_id,
                    'fee_group_assignment_id' => $item->fee_group_assignment_id,
                    'fee_discount_id' => $item->fee_discount_id,
                    'inventory_sale_id' => $item->inventory_sale_id,
                    'fee_type_name' => $feeTypeName,
                    'billing_month' => $month,
                    'billing_year' => $year,
                    'description' => "Previous balance from {$invoice->billing_month} {$invoice->billing_year}: {$item->description}",
                    'gross_amount' => $lineBalance,
                    'discount_amount' => 0,
                    'amount' => $lineBalance,
                    'is_previous_balance' => true,
                ];
            }
        }

        $amount = max(0, $priorMonthlyFees - (float) $priorInvoices->sum('payments_paid_amount'));

        if ($amount > 0 && empty($items)) {
            $items[] = [
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
                'description' => "Previous unpaid family balance from {$month} {$year}",
                'gross_amount' => $amount,
                'discount_amount' => 0,
                'amount' => $amount,
                'is_previous_balance' => true,
            ];
        }

        return [
            'amount' => $amount,
            'items' => $items,
            'period_label' => $unpaidPeriods
                ? implode(', ', array_unique($unpaidPeriods))
                : "{$month} {$year}",
        ];
    }

    private function billingMonthNumber(string $month): int
    {
        $index = array_search($month, array_keys(self::MONTHS), true);

        return $index === false ? 0 : $index + 1;
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
