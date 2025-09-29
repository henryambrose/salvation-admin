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
        Schema::create('niche_transfers', function (Blueprint $table) {
            $table->id();

            // Transfer reference and status
            $table->string('transfer_reference')->unique();
            $table->enum('status', ['pending', 'confirmed'])->default('pending');

            // Source and destination
            $table->foreignId('from_temporary_grave_id')->constrained('temporary_graves')->cascadeOnDelete();
            $table->foreignId('from_booking_id')->constrained('temporary_grave_bookings')->cascadeOnDelete();
            $table->foreignId('to_niche_id')->constrained('niches')->cascadeOnDelete();

            // Transfer details
            $table->date('transfer_request_date')->default(now());
            $table->date('proposed_transfer_date');
            $table->date('actual_transfer_date')->nullable();
            $table->text('transfer_reason');

            // Deceased person details (copied from temporary booking)
            $table->string('dead_first_name', 100);
            $table->string('dead_last_name', 100);
            $table->date('date_of_birth')->nullable();
            $table->date('died_on');
            $table->date('buried_on'); // Original burial date in temporary grave

            // Financial details
            $table->json('selected_services')->nullable();
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'completed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->text('payment_remarks')->nullable();

            // Transfer applicant (may be different from original applicant)
            $table->enum('applicant_type', ['member', 'external'])->default('external');
            $table->string('applicant_name');
            $table->string('contact_no');
            $table->string('contact_email')->nullable();
            $table->unsignedBigInteger('relationship_id')->nullable();
            $table->foreign('relationship_id')->references('id')->on('relationships');
            $table->text('applicant_address')->nullable();

            // Documents and permits
            $table->string('permit_no')->nullable();
            $table->json('required_documents')->nullable(); // List of required/submitted documents

            // Maintenance fields for the niche
            $table->decimal('pending_amount', 10, 2)->default(0);
            $table->integer('last_payment_year')->nullable();
            $table->json('partial_payment_months')->nullable();

            // Audit fields
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            // Indexes
            $table->index(['status', 'transfer_request_date']);
            $table->index(['proposed_transfer_date']);
            $table->index(['actual_transfer_date']);
            $table->index(['from_temporary_grave_id']);
            $table->index(['to_niche_id']);
            $table->index(['from_booking_id']);
            $table->index(['transfer_reference']);
            $table->index(['dead_first_name', 'dead_last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('niche_transfers');
    }
};
