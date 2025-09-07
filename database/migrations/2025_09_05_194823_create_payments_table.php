<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Unique payment reference
            $table->string('payment_reference', 50)->unique();

            // Polymorphic relationship to handle all booking types
            $table->string('payable_type'); // 'PermanentGraveBooking', 'TemporaryGraveBooking', 'NicheBooking'
            $table->unsignedBigInteger('payable_id');
            $table->index(['payable_type', 'payable_id']);

            // Payment amounts
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->decimal('concession_amount', 10, 2)->default(0);

            // Payment status
            $table->enum('payment_status', ['pending', 'partial', 'completed', 'refunded'])->default('pending');

            // Payment method reference
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');

            // Payment mode and transaction details
            $table->string('transaction_reference', 100)->nullable(); // cheque number, transaction ID, etc.
            $table->text('payment_notes')->nullable();

            // Services breakdown (JSON to store service details)
            $table->json('selected_services')->nullable(); // [{"service_id": 1, "quantity": 1, "unit_cost": 1000, "total_cost": 1000}]
            $table->json('service_charges')->nullable(); // Detailed breakdown for receipt

            // Payment date (when payment was actually made)
            $table->date('payment_date')->nullable();

            // Receipt details
            $table->string('receipt_number', 50)->nullable()->unique();
            $table->timestamp('receipt_generated_at')->nullable();

            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index('payment_status');
            $table->index('payment_date');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
