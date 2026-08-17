<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE teachers MODIFY COLUMN contract_type ENUM('Permanent', 'Temporary', 'Intern', 'Freelance', 'Part-Time', 'Full-Time', 'Contractor', 'Apprenticeship', 'Seasonal', 'Volunteer') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE teachers MODIFY COLUMN contract_type ENUM('Permanent', 'Temporary', 'Intern') NULL");
    }
};
