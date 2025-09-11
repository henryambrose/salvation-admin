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
        Schema::create('temporary_grave_bookings', function (Blueprint $table) {
            $table->id();

            // Booking reference and status
            $table->string('booking_reference')->unique();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');

            // Grave relationship
            $table->foreignId('temporary_grave_id')->constrained('temporary_graves')->cascadeOnDelete();

            // Dates
            $table->date('booking_date')->default(now());
            $table->date('died_on');
            $table->date('buried_on');

            // Deceased person details (no valid member required for temporary)
            $table->string('dead_first_name', 100)->nullable();
            $table->string('dead_last_name', 100)->nullable();
            $table->date('date_of_birth')->nullable();

            // Age details (alternative to date of birth)
            $table->integer('age')->nullable();
            $table->integer('months')->nullable();
            $table->integer('days')->nullable();

            // Personal details
            $table->foreignId('gender_id')->nullable()->constrained('genders')->nullOnDelete();
            $table->foreignId('deceased_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('nationality', 100)->nullable();
            $table->foreignId('parish_id')->nullable()->constrained('parishes')->nullOnDelete();

            // Death details
            $table->string('cause_of_death');
            $table->string('minister')->nullable();
            $table->text('special_requirements')->nullable();
            $table->text('remarks')->nullable();

            // Applicant details
            $table->enum('applicant_type', ['member', 'external'])->default('external');
            $table->foreignId('applicant_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('applicant_name');
            $table->string('contact_no');
            $table->string('contact_email')->nullable();
            $table->unsignedBigInteger('relationship_id')->nullable();
            $table->foreign('relationship_id')->references('id')->on('relationships');
            $table->string('permit_no')->nullable();

            // Financial details
            $table->json('selected_services')->nullable();
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'completed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->text('payment_remarks')->nullable();

            // Temporary grave specific fields
            $table->date('expected_transfer_date')->nullable(); // When this might be transferred to permanent/niche
            $table->boolean('transfer_requested')->default(false);

            // Audit fields
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            // Indexes
            $table->index(['status', 'booking_date']);
            $table->index(['buried_on']);
            $table->index(['temporary_grave_id', 'status']);
            $table->index(['dead_first_name', 'dead_last_name']);
            $table->index(['booking_reference']);
            $table->index(['expected_transfer_date']);
            $table->index(['transfer_requested']);
            $table->index('status');
            $table->index('booking_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporary_grave_bookings');
    }
};
