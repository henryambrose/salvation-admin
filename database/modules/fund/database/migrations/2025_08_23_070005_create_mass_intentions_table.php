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
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null');
            $table->foreignId('mass_intention_type_id')->constrained('mass_intention_types')->onDelete('cascade');
            $table->string('intention_for')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->string('external_name')->nullable();
            $table->string('phone', 20)->nullable();
            $table->date('mass_date')->nullable();
            $table->foreignId('mass_type_id')->nullable()->constrained('mass_types')->onDelete('set null');
            $table->text('special_instructions')->nullable();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');
            $table->string('transaction_reference')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            // Indexes
            $table->index('member_id');
            $table->index('status');
            $table->index('mass_date');
            $table->index(['status', 'mass_date']);
            $table->index('created_at');
            $table->index('phone');
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
