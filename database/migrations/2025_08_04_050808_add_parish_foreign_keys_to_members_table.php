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
        Schema::table('members', function (Blueprint $table) {
            // Add foreign key columns for parish relationships
            $table->foreignId('baptism_parish_id')->nullable()->constrained('parishes')->onDelete('set null');
            $table->foreignId('confirmation_parish_id')->nullable()->constrained('parishes')->onDelete('set null');
            $table->foreignId('marriage_parish_id')->nullable()->constrained('parishes')->onDelete('set null');
            $table->foreignId('death_parish_id')->nullable()->constrained('parishes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['baptism_parish_id']);
            $table->dropForeign(['confirmation_parish_id']);
            $table->dropForeign(['marriage_parish_id']);
            $table->dropForeign(['death_parish_id']);
            
            $table->dropColumn([
                'baptism_parish_id',
                'confirmation_parish_id', 
                'marriage_parish_id',
                'death_parish_id'
            ]);
        });
    }
};
