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
        Schema::create('obituary_condolences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obituary_page_id');
            $table->string('visitor_name', 100);
            $table->string('visitor_email', 100)->nullable();
            $table->text('message');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
            
            $table->foreign('obituary_page_id')->references('id')->on('obituary_pages')->onDelete('cascade');
            $table->index('obituary_page_id');
            $table->index('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obituary_condolences');
    }
};
