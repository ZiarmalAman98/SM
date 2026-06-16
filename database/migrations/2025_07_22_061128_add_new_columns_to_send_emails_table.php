<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
          Schema::table('send_emails', function (Blueprint $table) {
            $table->json('cc')->nullable()->after('to');
            $table->json('bcc')->nullable()->after('cc');
            $table->json('attachments')->nullable()->after('description');
            $table->boolean('is_urgent')->default(false)->after('attachments');
            $table->boolean('read_receipt')->default(false)->after('is_urgent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
           Schema::table('send_emails', function (Blueprint $table) {
            $table->dropColumn(['cc', 'bcc', 'attachments', 'is_urgent', 'read_receipt']);
        });
    }
};
