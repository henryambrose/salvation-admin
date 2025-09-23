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
        Schema::create('family_photos', function (Blueprint $table) {
            $table->id();
            $table->string('family_no')->index();
            $table->string('file_path', 500);
            $table->string('original_filename');
            $table->unsignedInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Index for family_no for performance (no unique constraint to allow soft delete/re-upload)
            $table->index('family_no');

            // Additional indexes for performance
            $table->index('uploaded_by');
            $table->index(['family_no', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_photos');
    }
};
