<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing audit triggers that are causing SQL errors
        DB::unprepared('DROP TRIGGER IF EXISTS members_audit_insert;');
        DB::unprepared('DROP TRIGGER IF EXISTS members_audit_update;');
        DB::unprepared('DROP TRIGGER IF EXISTS members_audit_delete;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't recreate the triggers in down() to avoid SQL errors
        // Use 'php artisan audit:generate-triggers [table_name]' to re-enable audit logging
    }
};
