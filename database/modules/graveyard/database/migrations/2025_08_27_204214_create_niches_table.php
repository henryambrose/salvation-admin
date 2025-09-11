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
        Schema::create('niches', function (Blueprint $table) {
            $table->id();
            $table->integer('niche_no')->unique()->index();
            $table->integer('sr_no')->index();
            $table->string('location')->nullable(); // Wall location, level, etc.
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->date('last_occupation_date')->nullable();
            $table->string('owner_name')->nullable()->index(); // For search functionality
            $table->string('contact_no')->nullable();
            $table->foreignId('member_id')->nullable()->references('id')->on('members')->onDelete('set null');
            $table->text('remarks')->nullable();
            $table->decimal('size_width', 8, 2)->nullable(); // in inches
            $table->decimal('size_height', 8, 2)->nullable(); // in inches
            $table->decimal('size_depth', 8, 2)->nullable(); // in inches
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Composite index for efficient queries
            $table->index(['niche_no', 'sr_no']);
            $table->index(['status', 'location']);
            $table->index('niche_no');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('niches');
    }
};
