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
        Schema::create('permission_category_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_category_id')->constrained()->onDelete('cascade');
            $table->string('rule_type'); // 'contains', 'starts_with', 'ends_with', 'regex'
            $table->string('rule_value'); // e.g., 'member', 'fund', '^create-.*'
            $table->integer('priority')->default(0); // Higher priority = checked first
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['permission_category_id', 'is_active']);
            $table->index(['rule_type', 'rule_value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_category_rules');
    }
};


