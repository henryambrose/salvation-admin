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
        Schema::table('permanent_grave_bookings', function (Blueprint $table) {
            // Modify the payment_status enum to include 'completed'
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'completed'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permanent_grave_bookings', function (Blueprint $table) {
            // Revert back to original enum values
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending')->change();
        });
    }
};
