<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing table
        Schema::dropIfExists('cells_and_association_members');
        
        // Create new table with proper many-to-many structure
        Schema::create('cells_and_association_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('cells_and_association_id')->constrained('cells_and_associations')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            // Add unique constraint to prevent duplicate associations
            $table->unique(['member_id', 'cells_and_association_id'], 'unique_member_cell_association');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cells_and_association_members');
    }
};
