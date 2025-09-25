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
        Schema::create('obituary_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obituary_page_id');
            $table->unsignedBigInteger('obituary_plan_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('service_type', ['basic', 'premium']);
            $table->enum('payment_status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('payment_reference', 100)->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->foreign('obituary_page_id')->references('id')->on('obituary_pages')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign keys
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('set null');
            $table->foreign('obituary_plan_id')->references('id')->on('obituary_plans')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');


            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            // Indexes
            $table->index('obituary_plan_id');
            $table->index('payment_method_id');
            $table->index('created_by');
            $table->index('updated_by');

            $table->index('obituary_page_id');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obituary_payments');
    }
};
