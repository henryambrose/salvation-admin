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
        Schema::table('members', function (Blueprint $table) {
            // Remove date and registration number columns
            $table->dropColumn([
                'baptism_date',
                'baptism_reg_no',
                'marriage_date',
                'marriage_reg_no',
                'death_date',
                'deaths_reg_no',
            ]);

            // NOTE: Keeping parish_id columns as they indicate sacraments outside main parish
            // baptism_parish_id, confirmation_parish_id, marriage_parish_id, death_parish_id remain
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Restore columns if rollback needed
            $table->date('baptism_date')->nullable()->after('baptism_parish_id');
            $table->string('baptism_reg_no')->nullable()->after('baptism_date');
            $table->date('marriage_date')->nullable()->after('marriage_parish_id');
            $table->string('marriage_reg_no')->nullable()->after('marriage_date');
            $table->date('death_date')->nullable()->after('death_parish_id');
            $table->string('deaths_reg_no')->nullable()->after('death_date');
        });
    }
};
