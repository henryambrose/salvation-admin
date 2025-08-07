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
        Schema::create('external_members', function (Blueprint $table) {
            $table->id();
            $table->string('first_name'); // Required
            $table->string('last_name')->nullable();
            $table->text('address')->nullable();
            $table->string('family_no'); // Default from initiating family
            $table->unsignedBigInteger('relationship_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('relationship_id')->references('id')->on('relationships')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index('family_no');
            $table->index('relationship_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_members');
    }
};
