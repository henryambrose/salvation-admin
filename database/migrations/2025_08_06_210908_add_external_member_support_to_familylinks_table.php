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
        Schema::table('familylinks', function (Blueprint $table) {
            // Add external member support
            $table->unsignedBigInteger('external_member_id')->nullable()->after('member_id');
            $table->unsignedBigInteger('related_external_member_id')->nullable()->after('related_member_id');
            
            // Foreign key constraints
            $table->foreign('external_member_id')->references('id')->on('external_members')->onDelete('cascade');
            $table->foreign('related_external_member_id')->references('id')->on('external_members')->onDelete('cascade');
            
            // Indexes
            $table->index('external_member_id');
            $table->index('related_external_member_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('familylinks', function (Blueprint $table) {
            $table->dropForeign(['external_member_id']);
            $table->dropForeign(['related_external_member_id']);
            $table->dropIndex(['external_member_id']);
            $table->dropIndex(['related_external_member_id']);
            $table->dropColumn(['external_member_id', 'related_external_member_id']);
        });
    }
};
