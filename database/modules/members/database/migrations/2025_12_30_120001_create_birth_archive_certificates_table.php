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
        Schema::create('birth_archive_certificates', function (Blueprint $table) {
            $table->id();

            // S3 Storage fields
            $table->string('folder_path'); // S3 folder location
            $table->string('file_name');   // Original filename

            // Registration fields
            $table->string('reg_year', 4)->index();  // Registration year
            $table->string('reg_no', 50)->index();   // Registration number

            // Birth date fields (store individually for filtering)
            $table->unsignedSmallInteger('birth_year')->index();     // 1800-2100
            $table->unsignedTinyInteger('birth_month')->index();     // 1-12
            $table->unsignedTinyInteger('birth_day')->index();       // 1-31

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
            $table->index(['birth_year', 'birth_month', 'birth_day'], 'birth_date_composite_idx');
            $table->index(['last_name', 'first_name'], 'birth_name_composite_idx');
            $table->index(['reg_year', 'reg_no'], 'birth_reg_composite_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birth_archive_certificates');
    }
};
