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
            $table->string('visitor_phone', 20)->nullable();
            $table->string('relationship', 50)->nullable();
            $table->string('visitor_ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->index('obituary_page_id');
            $table->foreign('obituary_page_id')->references('id')->on('obituary_pages')->onDelete('cascade');
            $table->text('notes')->nullable()->comment('Family thoughts, funeral mass details, months mind mass timing and place, condolence messages from family etc.');
            $table->index('is_approved');
            $table->timestamps();
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
