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
        Schema::create('obituary_background_themes', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // 'plain', 'gradient', 'memorial', etc.
            $table->string('name'); // 'Plain', 'Gradient', 'Memorial Sunset', etc.
            $table->text('description')->nullable();
            $table->string('type')->default('color'); // 'color', 'gradient', 'pattern', 'image'
            // $table->string('tier')->default('basic'); // 'basic', 'premium'
            $table->string('image_path')->nullable(); // Path to background image
            $table->json('style_properties')->nullable(); // CSS style properties as JSON
            $table->string('background_color')->nullable(); // Fallback background color
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obituary_background_themes');
    }
};
