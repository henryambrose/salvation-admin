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
        Schema::create('community_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contribution_type_id');
            $table->date('collection_date');
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->string('location')->nullable();
            $table->unsignedBigInteger('collected_by_user_id')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('recorded'); // recorded, verified, archived
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('contribution_type_id')->references('id')->on('community_contribution_types');
            $table->foreign('collected_by_user_id')->references('id')->on('users');

            // Indexes for performance
            $table->index(['collection_date', 'contribution_type_id'], 'cc_date_type_idx');
            $table->index(['status', 'collection_date'], 'cc_status_date_idx');
            $table->index('contribution_type_id', 'cc_type_idx');
            $table->index('collected_by_user_id', 'cc_collector_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_contributions');
    }
};
