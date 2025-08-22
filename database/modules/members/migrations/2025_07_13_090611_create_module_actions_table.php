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
        Schema::create('module_actions', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // e.g. 'create', 'view', 'edit', 'delete'
            $table->string('name'); // e.g. 'Create Member', 'View Community', etc.
            $table->string('slug'); // e.g. 'create-member', 'view-community', etc.
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['module_id', 'action']); // Ensure unique action per module
            $table->unique(['module_id', 'slug']); // Ensure unique slug per module action
            $table->index(['module_id', 'action']); // Index for faster lookups
            $table->index(['module_id', 'slug']); // Index for faster lookups by slug
            $table->softDeletes(); // Optional: if you want to support soft deletes
            $table->comment('Table to store actions for each module, e.g. create, view, edit, delete actions for each module');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_actions');
    }
};
