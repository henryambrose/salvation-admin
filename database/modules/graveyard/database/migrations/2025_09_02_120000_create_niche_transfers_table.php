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
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            
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
            $table->string('deceased_first_name', 100);
            $table->string('deceased_last_name', 100);
            $table->date('deceased_date_of_birth')->nullable();
            $table->date('deceased_died_on');
            $table->date('deceased_buried_on'); // Original burial date in temporary grave
            
            // Transfer applicant (may be different from original applicant)
            $table->string('transfer_applicant_name');
            $table->string('transfer_contact_no');
            $table->string('transfer_contact_email')->nullable();
            $table->string('relationship_to_deceased')->nullable();
            $table->text('applicant_address')->nullable();
            
            // Financial details for transfer
            $table->json('selected_services')->nullable(); // Services for transfer process
            $table->decimal('transfer_cost', 10, 2)->default(0); // Cost for transfer service
            $table->decimal('niche_cost', 10, 2)->default(0); // Cost of the niche itself
            $table->decimal('total_cost', 10, 2)->default(0); // Total cost
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->text('payment_remarks')->nullable();
            
            // Administrative details
            $table->text('admin_notes')->nullable(); // Notes from admin reviewing transfer
            $table->string('approved_by')->nullable(); // User who approved the transfer
            $table->date('approval_date')->nullable();
            $table->text('rejection_reason')->nullable(); // If rejected
            $table->string('rejected_by')->nullable();
            $table->date('rejection_date')->nullable();
            
            // Documents and permits
            $table->string('transfer_permit_no')->nullable();
            $table->json('required_documents')->nullable(); // List of required/submitted documents
            
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
            $table->index(['deceased_first_name', 'deceased_last_name']);
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