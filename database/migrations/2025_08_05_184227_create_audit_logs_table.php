<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create audit_logs table
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 100);
            $table->enum('action', ['CREATE', 'UPDATE', 'DELETE']);
            $table->unsignedBigInteger('record_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['table_name', 'action']);
            $table->index('user_id');
            $table->index('created_at');
        });

        // Note: Triggers are not created automatically to avoid SQL errors
        // Use 'php artisan audit:generate-triggers [table_name]' to enable audit logging
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop audit_logs table
        Schema::dropIfExists('audit_logs');
    }
};
