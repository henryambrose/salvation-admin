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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. 'Member', 'Community', etc.
            $table->string('slug')->unique(); // e.g. 'member', 'community', etc.
            $table->string('icon')->nullable(); // Optional icon for the module
            $table->boolean('is_active')->default(true); // To enable/disable the module
            $table->integer('sort_order')->default(0); // To control the display order
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
