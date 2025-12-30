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
        Schema::create('death_archive_certificates', function (Blueprint $table) {
            $table->id();

            // S3 Storage fields
            $table->string('folder_path'); // S3 folder location
            $table->string('file_name');   // Original filename

            // Registration fields
            $table->string('reg_year', 4)->index();  // Registration year
            $table->string('reg_no', 50)->index();   // Registration number

            // Death date fields (store individually for filtering)
            $table->unsignedSmallInteger('death_year')->index();     // 1800-2100
            $table->unsignedTinyInteger('death_month')->index();     // 1-12
            $table->unsignedTinyInteger('death_day')->index();       // 1-31

            // Name fields
            $table->string('first_name')->index();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->index();

            // Additional info
            $table->text('notes')->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Composite indexes for common queries
            $table->index(['death_year', 'death_month', 'death_day'], 'death_date_composite_idx');
            $table->index(['last_name', 'first_name'], 'death_name_composite_idx');
            $table->index(['reg_year', 'reg_no'], 'death_reg_composite_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('death_archive_certificates');
    }
};
