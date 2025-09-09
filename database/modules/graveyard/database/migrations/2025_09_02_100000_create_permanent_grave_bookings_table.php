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
        Schema::create('permanent_grave_bookings', function (Blueprint $table) {
            $table->id();

            // Booking reference and status
            $table->string('booking_reference')->unique();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');

            // Grave and Valid Member (Required for permanent graves)
            $table->foreignId('permanent_grave_id')->constrained('permanent_graves')->cascadeOnDelete();
            $table->foreignId('valid_member_id')->constrained('valid_members')->cascadeOnDelete();

            // Dates
            $table->date('booking_date')->default(now());
            $table->date('died_on');
            $table->date('buried_on');

            // Death details specific to permanent grave bookings
            $table->string('cause_of_death');
            $table->string('minister')->nullable();
            $table->text('special_requirements')->nullable();
            $table->text('remarks')->nullable();

            // Applicant details (who is applying for the burial)
            $table->enum('applicant_type', ['member', 'external'])->default('member');
            $table->string('applicant_name');
            $table->string('contact_no');
            $table->string('contact_email')->nullable();
            $table->string('permit_no')->nullable();

            // Financial details
            $table->json('selected_services')->nullable(); // Array of service IDs with quantities
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'completed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->text('payment_remarks')->nullable();

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
            $table->index(['permanent_grave_id', 'status']);
            $table->index(['valid_member_id']);
            $table->index(['booking_reference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permanent_grave_bookings');
    }
};
