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
        Schema::create('permanent_graves', function (Blueprint $table) {
            $table->id();
            $table->string('block')->index();
            $table->integer('row')->index();
            $table->integer('column')->index();
            $table->string('old_no')->nullable();
            $table->enum('status', ['available', 'unavailable'])->default('available')->index();
            $table->date('last_burial_date')->nullable();
            $table->string('owner_name')->nullable()->index(); // For search functionality
            $table->string('contact_no')->nullable();
            $table->foreignId('member_id')->nullable()->references('id')->on('members')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->decimal('plot_size', 8, 2)->nullable(); // in square feet
            $table->boolean('is_active')->default(true);
            $table->decimal('pending_amount', 10, 2)->default(0);
            $table->integer('last_payment_year')->nullable();
            $table->json('partial_payment_months')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Composite indexes for efficient queries
            $table->index(['block', 'row', 'column']);
            $table->index(['status', 'block']);

            // Unique constraint to prevent duplicate graves
            $table->unique(['block', 'row', 'column']);

            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permanent_graves');
    }
};
