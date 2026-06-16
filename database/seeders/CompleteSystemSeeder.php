<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CompleteSystemSeeder extends Seeder
{
    private const RECORDS_PER_TABLE = 5;

    private array $skipTables = [
        'migrations',
    ];

    private array $jsonColumns = [
        'failed_import_rows' => ['data'],
        'filament_email_log' => ['attachments'],
        'send_emails' => ['cc', 'bcc', 'attachments'],
    ];

    public function run(): void
    {
        $tables = $this->tables();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        foreach ($tables as $table) {
            $this->seedTable($table);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function seedTable(string $table): void
    {
        $columns = $this->columns($table);
        $rows = [];

        for ($i = 1; $i <= self::RECORDS_PER_TABLE; $i++) {
            $row = [];

            foreach ($columns as $column) {
                if ($column->extra === 'auto_increment') {
                    continue;
                }

                $row[$column->name] = $this->valueFor($table, $column, $i);
            }

            $rows[] = $row;
        }

        DB::table($table)->insert($rows);
    }

    private function tables(): array
    {
        return collect(DB::select('SHOW TABLES'))
            ->map(fn ($row) => array_values((array) $row)[0])
            ->reject(fn ($table) => in_array($table, $this->skipTables, true))
            ->values()
            ->all();
    }

    private function columns(string $table): array
    {
        $database = DB::getDatabaseName();

        return collect(DB::select(
            'SELECT COLUMN_NAME, IS_NULLABLE, COLUMN_DEFAULT, DATA_TYPE, COLUMN_TYPE, COLUMN_KEY, EXTRA
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
             ORDER BY ORDINAL_POSITION',
            [$database, $table]
        ))->map(function ($column) {
            return (object) [
                'name' => $column->COLUMN_NAME,
                'nullable' => $column->IS_NULLABLE === 'YES',
                'default' => $column->COLUMN_DEFAULT,
                'type' => $column->DATA_TYPE,
                'columnType' => $column->COLUMN_TYPE,
                'key' => $column->COLUMN_KEY,
                'extra' => $column->EXTRA,
            ];
        })->all();
    }

    private function valueFor(string $table, object $column, int $i): mixed
    {
        if (in_array($column->name, ['created_at', 'updated_at', 'completed_at', 'cancelled_at', 'finished_at'], true)
            && in_array($column->type, ['int', 'bigint'], true)) {
            return now()->timestamp - (self::RECORDS_PER_TABLE - $i) * 86400;
        }

        $custom = $this->customValue($table, $column->name, $i);

        if ($custom !== null) {
            return $custom;
        }

        return match ($column->name) {
            'created_at', 'updated_at', 'email_verified_at', 'completed_at', 'submitted_at', 'failed_at' => now()->subDays(self::RECORDS_PER_TABLE - $i),
            'starts_at', 'ends_at', 'entry_time', 'exit_time', 'complaint_date' => now()->addHours($i),
            'date', 'dob', 'child_dob', 'start_date', 'end_date', 'joining_date', 'date_of_birth',
            'borrow_date', 'return_date', 'allocation_date', 'transfer_date', 'payment_date',
            'admission_date', 'effective_date', 'date_of_report', 'leave_start_date', 'leave_end_date' => now()->subDays($i)->toDateString(),
            'time', 'start_time', 'end_time', 'work_from', 'work_to' => sprintf('%02d:00:00', 8 + $i),
            'academic_year', 'year' => 2026,
            'month' => $i,
            'password' => Hash::make('password'),
            'remember_token', 'token', 'uuid' => Str::random(40) . $i,
            'id' => $column->type === 'char' ? (string) Str::uuid() : $i,
            default => $this->defaultValue($table, $column, $i),
        };
    }

    private function defaultValue(string $table, object $column, int $i): mixed
    {
        if ($column->type === 'enum') {
            return $this->enumValues($column->columnType)[$i - 1] ?? $this->enumValues($column->columnType)[0];
        }

        if ($column->type === 'json' || $this->isJsonColumn($table, $column->name)) {
            return json_encode(['sample' => true, 'row' => $i]);
        }

        if (Str::endsWith($column->name, '_id') || in_array($column->name, ['from_id', 'to_id', 'model_id', 'favorite_id', 'person_to_meet'], true)) {
            return $i;
        }

        if ($column->name === 'status' && in_array($column->type, ['varchar', 'char', 'text'], true)) {
            return 'active';
        }

        if (str_contains($column->name, 'email')) {
            return Str::slug($table . '-' . $column->name . '-' . $i) . '@school.test';
        }

        if (str_contains($column->name, 'phone') || str_contains($column->name, 'contact')) {
            return '+9370000000' . $i;
        }

        if (str_contains($column->name, 'amount') || str_contains($column->name, 'salary') || str_contains($column->name, 'fees')) {
            return 1000 + ($i * 250);
        }

        if (in_array($column->type, ['tinyint', 'boolean'], true)) {
            return $i % 2;
        }

        if (in_array($column->type, ['int', 'bigint', 'smallint'], true)) {
            return $i;
        }

        if (in_array($column->type, ['decimal', 'double', 'float'], true)) {
            return 10 * $i;
        }

        if (in_array($column->type, ['date'], true)) {
            return now()->subDays($i)->toDateString();
        }

        if (in_array($column->type, ['datetime', 'timestamp'], true)) {
            return now()->subDays($i);
        }

        if ($column->type === 'time') {
            return sprintf('%02d:00:00', 8 + $i);
        }

        if ($column->type === 'year') {
            return 2026;
        }

        return $this->fitString($this->humanText($table, $column->name, $i), $column);
    }

    private function customValue(string $table, string $column, int $i): mixed
    {
        $data = [
            'users' => [
                'name' => ['Ahmad', 'Fatima', 'Mohammad', 'Aisha', 'Hassan'],
                'last_name' => ['Rahimi', 'Karimi', 'Ahmadi', 'Noori', 'Safi'],
                'father_name' => ['Abdul Rahman', 'Mohammad Nabi', 'Sayed Karim', 'Ghulam Ali', 'Hamidullah'],
                'email' => ['admin@school.test', 'teacher@school.test', 'staff@school.test', 'student@school.test', 'guardian@school.test'],
                'type' => ['admin', 'teacher', 'staff', 'student', 'guardian'],
                'branch_id' => [1, 1, 2, 3, 4],
                'active_status' => [1, 1, 1, 1, 1],
            ],
            'branches' => [
                'branch_name' => ['Kabul Main Campus', 'Herat Campus', 'Mazar Campus', 'Kandahar Campus', 'Nangarhar Campus'],
                'branch_address' => ['Shahr-e-Naw, Kabul', 'Darwaza Khush, Herat', 'Kart-e-Ariana, Mazar', 'Aino Mina, Kandahar', 'Jalalabad City'],
                'branch_manager_name' => ['Samiullah Rahimi', 'Farid Ahmad', 'Nasir Khan', 'Latifa Noori', 'Wali Mohammad'],
                'monthly_budget' => [55000, 42000, 38000, 36000, 34000],
            ],
            'school_classes' => [
                'class_name' => ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5'],
                'description' => ['Primary class level one', 'Primary class level two', 'Primary class level three', 'Primary class level four', 'Primary class level five'],
                'teacher_id' => [2, 2, 2, 2, 2],
            ],
            'sections' => ['name' => ['A', 'B', 'C', 'D', 'E']],
            'student_categories' => [
                'name' => ['Regular', 'Scholarship', 'Orphan Support', 'Staff Child', 'Transfer'],
            ],
            'students' => [
                'admission_no' => ['ADM-2026-001', 'ADM-2026-002', 'ADM-2026-003', 'ADM-2026-004', 'ADM-2026-005'],
                'roll_no' => ['R001', 'R002', 'R003', 'R004', 'R005'],
                'user_id' => [1, 2, 3, 4, 5],
                'category_id' => [1, 2, 3, 4, 5],
                'section_id' => [1, 2, 3, 4, 5],
                'gender' => ['Male', 'Female', 'Male', 'Female', 'Male'],
                'blood_group' => ['O+', 'A+', 'B+', 'AB+', 'O-'],
                'address' => ['Kabul, Afghanistan', 'Herat, Afghanistan', 'Mazar, Afghanistan', 'Kandahar, Afghanistan', 'Nangarhar, Afghanistan'],
            ],
            'parent_student' => [
                'student_id' => [1, 2, 3, 4, 5],
                'parent_guardian_id' => [1, 2, 3, 4, 5],
                'relationship' => ['father', 'mother', 'father', 'mother', 'other'],
            ],
            'parent_guardians' => [
                'family_code' => ['FAM-000001', 'FAM-000002', 'FAM-000003', 'FAM-000004', 'FAM-000005'],
            ],
            'fee_types' => [
                'name' => ['Admission Fee', 'Monthly Tuition', 'Exam Fee', 'Transport Fee', 'Library Fee'],
                'default_amount' => [1500, 3000, 700, 1200, 400],
            ],
            'fee_groups' => [
                'group_name' => ['New Admission', 'Monthly Fees', 'Exam Charges', 'Transport Charges', 'Library Charges'],
            ],
            'fee_discounts' => [
                'discount_name' => ['Sibling Discount', 'Early Payment', 'Scholarship Support', 'Staff Child Discount', 'Need Based Support'],
                'discount_code' => ['SIBLING10', 'EARLY05', 'SCHOLAR25', 'STAFF50', 'NEED15'],
                'discount_value' => [10, 5, 25, 50, 15],
            ],
            'subjects' => [
                'name' => ['Mathematics', 'Science', 'English', 'Dari', 'Computer'],
                'teacher_id' => [2, 2, 2, 2, 2],
            ],
            'expense_categories' => ['name' => ['Utilities', 'Stationery', 'Maintenance', 'Transport Fuel', 'Office Supplies']],
            'income_sources' => ['name' => ['Tuition Fees', 'Admission Fees', 'Donations', 'Transport Fees', 'Book Sales']],
            'question_difficulties' => ['level' => ['Easy', 'Medium', 'Hard', 'Advanced', 'Review']],
            'question_languages' => ['language' => ['English', 'Dari', 'Pashto', 'Arabic', 'Urdu']],
            'roles' => ['name' => ['admin', 'teacher', 'staff', 'student', 'guardian'], 'guard_name' => ['web', 'web', 'web', 'web', 'web']],
            'permissions' => ['name' => ['view dashboard', 'manage students', 'manage fees', 'manage exams', 'manage reports'], 'guard_name' => ['web', 'web', 'web', 'web', 'web']],
            'vehicles' => [
                'vehicle_number' => ['KBL-1001', 'KBL-1002', 'HER-2001', 'MZR-3001', 'KDR-4001'],
                'type' => ['bus', 'van', 'bus', 'car', 'van'],
                'driver_name' => ['Nematullah', 'Qudratullah', 'Zabihullah', 'Rahmat Shah', 'Javed Khan'],
            ],
            'routes' => [
                'name' => ['Shahr-e-Naw Route', 'Kart-e-Char Route', 'Darulaman Route', 'Khair Khana Route', 'Barchi Route'],
            ],
            'stock_types' => ['name' => ['Books', 'Uniforms', 'Stationery', 'Lab Equipment', 'Sports Items']],
            'materials' => ['name' => ['Math Textbook', 'School Uniform', 'Notebook Pack', 'Microscope Kit', 'Football']],
            'exam_types' => ['name' => ['Midterm', 'Final', 'Monthly Test', 'Practical', 'Oral Exam']],
            'grade_systems' => ['grade' => ['A', 'B', 'C', 'D', 'F']],
            'app_settings' => [
                'tab' => ['general', 'academic', 'finance', 'notification', 'transport'],
                'key' => ['school_name', 'academic_year', 'currency', 'email_enabled', 'transport_enabled'],
                'value' => ['Cosmos School', '2026', 'AFN', 'true', 'true'],
            ],
        ];

        return $data[$table][$column][$i - 1] ?? null;
    }

    private function enumValues(string $columnType): array
    {
        preg_match_all("/'([^']+)'/", $columnType, $matches);

        return $matches[1] ?: ['active'];
    }

    private function isJsonColumn(string $table, string $column): bool
    {
        return in_array($column, $this->jsonColumns[$table] ?? [], true);
    }

    private function humanText(string $table, string $column, int $i): string
    {
        $label = Str::of($column)->replace('_', ' ')->title();
        $tableName = Str::of($table)->replace('_', ' ')->title();

        return "{$label} {$i} for {$tableName}";
    }

    private function fitString(string $value, object $column): string
    {
        preg_match('/\((\d+)\)/', $column->columnType, $matches);
        $maxLength = isset($matches[1]) ? (int) $matches[1] : null;

        if ($maxLength === null || strlen($value) <= $maxLength) {
            return $value;
        }

        return substr($value, 0, $maxLength);
    }
}
