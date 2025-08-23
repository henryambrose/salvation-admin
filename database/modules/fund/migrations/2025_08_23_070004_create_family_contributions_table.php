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
            $table->integer('year');
            $table->decimal('amount', 10, 2);
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');
            $table->foreignId('fund_category_id')->nullable()->constrained('fund_categories')->onDelete('set null');
            $table->date('payment_date');
            $table->enum('status', ['pending', 'paid', 'partial', 'cancelled', 'refunded'])->default('pending');
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null');
            $table->string('paid_by_name', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['family_no', 'year']);
            $table->index(['year', 'status']);
            $table->index('payment_date');
            $table->index('member_id');
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
