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
        Schema::create('grave_bookings', function (Blueprint $table) {
            $table->id();
            
            // Grave selection
            $table->enum('grave_type', ['permanent', 'temporary'])->index();
            $table->unsignedBigInteger('permanent_grave_id')->nullable();
            $table->unsignedBigInteger('temporary_grave_id')->nullable();
            
            // Deceased person details
            $table->string('dead_first_name');
            $table->string('dead_last_name');
            $table->date('date_of_birth')->nullable();
            $table->integer('age')->nullable();
            $table->integer('months')->nullable();
            $table->integer('days')->nullable();
            $table->date('died_on');
            $table->date('buried_on');
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->text('cause_of_death')->nullable();
            $table->string('nationality')->default('Indian');
            $table->text('remarks')->nullable();
            
            // Parish and religious details
            $table->unsignedBigInteger('parish_id')->nullable();
            $table->string('minister')->nullable();
            
            // Relationship and applicant details
            $table->enum('relationship', ['member', 'non_member'])->default('non_member');
            $table->enum('applicant_type', ['member', 'non_member'])->default('non_member');
            $table->unsignedBigInteger('member_id')->nullable(); // If deceased is a member
            $table->unsignedBigInteger('applicant_member_id')->nullable(); // If applicant is a member
            $table->string('applicant_name')->nullable(); // If non-member applicant
            $table->string('contact_no')->nullable();
            
            // BMC and administrative details
            $table->string('permit_no')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending')->index();
            
            // Financial details
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->text('payment_remarks')->nullable();
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending')->index();
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for efficient queries
            $table->index(['grave_type', 'status']);
            $table->index(['buried_on', 'status']);
            $table->index(['dead_first_name', 'dead_last_name']);
            $table->index(['permit_no']);
            $table->index(['payment_status', 'status']);

            // Foreign key constraints
            $table->foreign('permanent_grave_id')->references('id')->on('permanent_graves')->onDelete('set null');
            $table->foreign('temporary_grave_id')->references('id')->on('temporary_graves')->onDelete('set null');
            $table->foreign('gender_id')->references('id')->on('genders')->onDelete('set null');
            $table->foreign('parish_id')->references('id')->on('parishes')->onDelete('set null');
            $table->foreign('member_id')->references('id')->on('members')->onDelete('set null');
            $table->foreign('applicant_member_id')->references('id')->on('members')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Note: Business logic will ensure only one grave type is selected
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grave_bookings');
    }
};