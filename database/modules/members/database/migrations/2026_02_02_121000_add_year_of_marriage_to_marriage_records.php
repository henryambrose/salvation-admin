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
        Schema::table('marriage_records', function (Blueprint $table) {
            if (!Schema::hasColumn('marriage_records', 'marriage_reg_year')) {
                $table->string('marriage_reg_year', 100)->nullable()->after('marriage_reg_no');
            }
            if (!Schema::hasColumn('marriage_records', 'marriage_date_text')) {
                $table->string('marriage_date_text', 100)->nullable()->after('marriage_reg_year');
            }
            if (!Schema::hasColumn('marriage_records', 'bride_dob_text')) {
                $table->string('bride_dob_text', 100)->nullable()->after('bride_dob');
            }
            if (!Schema::hasColumn('marriage_records', 'bridegroom_dob_text')) {
                $table->string('bridegroom_dob_text', 100)->nullable()->after('bridegroom_dob');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marriage_records', function (Blueprint $table) {
            $table->dropColumn('year_of_marriage');
        });
    }
};
