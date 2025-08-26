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
        Schema::create('mass_intentions', function (Blueprint $table) {
            $table->id();
            $table->string('family_no', 50);
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null');
            $table->foreignId('mass_schedule_id')->constrained('mass_schedules')->onDelete('cascade');
            $table->foreignId('mass_intention_type_id')->constrained('mass_intention_types')->onDelete('cascade');
            $table->string('intention_for');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['family_no', 'mass_schedule_id']);
            $table->index('member_id');
            $table->index('mass_schedule_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mass_intentions');
    }
};
