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
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('service_type_id');
            $table->decimal('amount', 10, 2)->default(0); // Actual amount charged (may differ from service type cost)
            $table->decimal('original_cost', 10, 2)->default(0); // Original service cost for reference
            $table->integer('quantity')->default(1);
            $table->text('remarks')->nullable();
            $table->boolean('is_complimentary')->default(false); // Free service
            $table->timestamps();

            // Indexes for efficient queries
            $table->index(['booking_id', 'service_type_id']);
            $table->index(['service_type_id']);

            // Foreign key constraints
            $table->foreign('booking_id')->references('id')->on('grave_bookings')->onDelete('cascade');
            $table->foreign('service_type_id')->references('id')->on('service_types')->onDelete('cascade');

            // Ensure no duplicate services per booking
            $table->unique(['booking_id', 'service_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_services');
    }
};