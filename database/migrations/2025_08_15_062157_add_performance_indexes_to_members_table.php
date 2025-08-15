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
            // Composite index for family search with community and deleted_at
            $table->index(['family_no', 'community_id', 'deleted_at'], 'idx_members_family_search');
            
            // Composite index for name search with deleted_at
            $table->index(['first_name', 'last_name', 'deleted_at'], 'idx_members_name_search');
            
            // Composite index for common filters
            $table->index(['community_id', 'relationship_id', 'gender_id', 'deleted_at'], 'idx_members_filters');
            
            // Index for date of birth (age calculation)
            $table->index(['date_of_birth', 'deleted_at'], 'idx_members_dob');
            
            // Index for member number search
            $table->index(['member_no', 'deleted_at'], 'idx_members_member_no');
            
            // Index for blood group
            $table->index(['blood_group_id', 'deleted_at'], 'idx_members_blood_group');
            
            // Index for age group filtering
            $table->index(['deleted_at'], 'idx_members_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex('idx_members_family_search');
            $table->dropIndex('idx_members_name_search');
            $table->dropIndex('idx_members_filters');
            $table->dropIndex('idx_members_dob');
            $table->dropIndex('idx_members_member_no');
            $table->dropIndex('idx_members_blood_group');
            $table->dropIndex('idx_members_deleted_at');
        });
    }
};
