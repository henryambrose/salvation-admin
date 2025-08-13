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
        Schema::table('cells_and_association_members', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate member-cell association combinations
            $table->unique(['member_id', 'cells_and_association_id'], 'unique_member_cell_association');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cells_and_association_members', function (Blueprint $table) {
            // Remove the unique constraint
            $table->dropUnique('unique_member_cell_association');
        });
    }
};
