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
        Schema::table('baptism_records', function (Blueprint $table) {
            if (!Schema::hasColumn('baptism_records', 'baptism_date_text')) {
                $table->string('baptism_date_text')->nullable()->after('baptism_date');
            }
            if (!Schema::hasColumn('baptism_records', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('baptism_date');
            }
            if (!Schema::hasColumn('baptism_records', 'birth_date_text')) {
                $table->string('birth_date_text')->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('baptism_records', 'baptism_reg_year')) {
                $table->string('baptism_reg_year')->nullable()->after('baptism_date_text');
            }
            if (!Schema::hasColumn('baptism_records', 'confirmation')) {
                $table->string('confirmation')->nullable()->after('confirmation_date');
            }
            if (!Schema::hasColumn('baptism_records', 'marriage_on')) {
                $table->string('marriage_on')->nullable()->after('baptism_remarks');
            }
            if (!Schema::hasColumn('baptism_records', 'marriage_at')) {
                $table->string('marriage_at')->nullable()->after('marriage_on');
            }
            if (!Schema::hasColumn('baptism_records', 'marriage_to')) {
                $table->string('marriage_to')->nullable()->after('marriage_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baptism_records', function (Blueprint $table) {
            $table->dropColumn(['baptism_date_text', 'birth_date', 'birth_date_text', 'baptism_reg_year', 'confirmation', 'marriage_on', 'marriage_at', 'marriage_to']);
        });
    }
};
