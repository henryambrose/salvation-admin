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
        Schema::create('certificate_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('certificate_type_id')->nullable()->constrained('certificate_types')->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('certificate_templates')->onDelete('set null');
            $table->date('issued_date');
            $table->foreignId('issued_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('certificate_number')->unique();
            $table->json('additional_data')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_hash')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamp('last_downloaded_at')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_reprint')->default(false);
            $table->foreignId('original_certificate_id')->nullable()->constrained('certificate_records')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['member_id', 'certificate_type_id']);
            $table->index('certificate_type_id');
            $table->index('issued_date');
            $table->index('certificate_number');
            $table->index('is_reprint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificate_records');
    }
};
