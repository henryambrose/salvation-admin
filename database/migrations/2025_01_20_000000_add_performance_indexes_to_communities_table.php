<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            // Add indexes for frequently searched fields
            $table->index('name');
            $table->index('zone_id');
            $table->index(['deleted_at', 'created_at']);
            
            // Composite index for common queries
            $table->index(['deleted_at', 'zone_id']);
        });
        
        // Add indexes to related tables
        Schema::table('p_p_c_heads', function (Blueprint $table) {
            $table->index(['community_id', 'deleted_at']);
        });
        
        Schema::table('s_c_c_heads', function (Blueprint $table) {
            $table->index(['community_id', 'deleted_at']);
        });
        
        Schema::table('members', function (Blueprint $table) {
            $table->index(['first_name', 'last_name']);
            $table->index(['community_id', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('communities', function (Blueprint $table) {
            $table->dropIndex(['name']);
            $table->dropIndex(['zone_id']);
            $table->dropIndex(['deleted_at', 'created_at']);
            $table->dropIndex(['deleted_at', 'zone_id']);
        });
        
        Schema::table('p_p_c_heads', function (Blueprint $table) {
            $table->dropIndex(['community_id', 'deleted_at']);
        });
        
        Schema::table('s_c_c_heads', function (Blueprint $table) {
            $table->dropIndex(['community_id', 'deleted_at']);
        });
        
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['first_name', 'last_name']);
            $table->dropIndex(['community_id', 'deleted_at']);
        });
    }
};
