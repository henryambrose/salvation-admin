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
        Schema::create('family_contributions', function (Blueprint $table) {
            $table->id();
            $table->string('family_no', 50);
            // Date range instead of single year
            $table->decimal('amount', 10, 2);
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');
            $table->foreignId('fund_category_id')->nullable()->constrained('fund_categories')->onDelete('set null');
            // Start and end date for contribution period
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending', 'paid', 'partial', 'cancelled', 'refunded'])->default('pending');
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null');
            $table->string('paid_by_name', 255)->nullable();
            $table->string('contact_no', 20)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['family_no', 'start_date', 'end_date']);
            $table->index(['status']);
            $table->index(['start_date']);
            $table->index(['end_date']);
            $table->index('member_id');
            $table->index('family_no');
            $table->index(['start_date', 'end_date']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_contributions');
    }
};
