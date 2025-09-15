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
        Schema::create('obituary_pages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('qr_code_path')->nullable();

            // Link to either booking type
            $table->unsignedBigInteger('permanent_grave_booking_id')->nullable();
            $table->unsignedBigInteger('temporary_grave_booking_id')->nullable();

            // Obituary content
            $table->text('biography')->nullable();
            $table->text('favorite_memory')->nullable();
            $table->text('achievements')->nullable();
            $table->text('hobbies_interests')->nullable();
            $table->text('notes')->nullable();

            // Media
            $table->string('profile_image')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('audio_message')->nullable();

            // Customization
            $table->string('theme_color', 7)->default('#000000');
            $table->string('background_style')->deafult('plain');

            // Visitor features
            $table->boolean('allow_condolences')->default(true);
            $table->boolean('allow_memory_sharing')->default(true);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_active')->default(true);

            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('published_by')->nullable();

            $table->foreign('published_by')->references('id')->on('users')->onDelete('set null');
            $table->index('is_published');

            // Service details
            $table->enum('service_type', ['basic', 'premium'])->default('basic');
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            // Analytics
            $table->integer('view_count')->default(0);
            $table->integer('qr_scan_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('uuid');
            $table->index('permanent_grave_booking_id', 'obituary_permanent_booking_idx');
            $table->index('temporary_grave_booking_id', 'obituary_temporary_booking_idx');

            // Foreign keys
            $table->foreign('permanent_grave_booking_id')->references('id')->on('permanent_grave_bookings')->onDelete('cascade');
            $table->foreign('temporary_grave_booking_id')->references('id')->on('temporary_grave_bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obituary_pages');
    }
};
