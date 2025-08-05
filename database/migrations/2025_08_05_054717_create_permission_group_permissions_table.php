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
        Schema::create('permission_group_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_group_id')->constrained('permission_groups')->onDelete('cascade');
            $table->foreignId('module_action_id')->constrained('module_actions')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combinations
            $table->unique(['permission_group_id', 'module_action_id'], 'pg_permissions_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_group_permissions');
    }
};
