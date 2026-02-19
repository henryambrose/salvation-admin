<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE remains_transfers MODIFY COLUMN transfer_reason TEXT NULL");
        DB::statement("ALTER TABLE remains_transfers MODIFY COLUMN applicant_name VARCHAR(255) NULL");
        DB::statement("ALTER TABLE remains_transfers MODIFY COLUMN contact_no VARCHAR(255) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE remains_transfers MODIFY COLUMN transfer_reason TEXT NOT NULL");
        DB::statement("ALTER TABLE remains_transfers MODIFY COLUMN applicant_name VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE remains_transfers MODIFY COLUMN contact_no VARCHAR(255) NOT NULL");
    }
};
