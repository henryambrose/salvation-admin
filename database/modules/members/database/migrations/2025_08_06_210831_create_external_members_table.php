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
        Schema::create('external_members', function (Blueprint $table) {
            $table->id();
            $table->string('external_member_no')->unique()->nullable();

            $table->string('first_name'); // Required
            $table->string('last_name')->nullable();
            $table->text('address')->nullable();
            $table->string('family_no'); // Default from initiating family
            // Optional direct community link for performance (denormalized)
            $table->foreignId('community_id')->nullable()->constrained('communities');
            $table->foreignId('relationship_id')->constrained('relationships')->onDelete('cascade');
            $table->unsignedBigInteger('father_id')->nullable();
            $table->unsignedBigInteger('mother_id')->nullable();
            $table->unsignedBigInteger('spouse_id')->nullable();
            $table->string('father_source')->nullable();
            $table->string('mother_source')->nullable();
            $table->string('spouse_source')->nullable();
            $table->index('external_member_no');
            $table->foreignId('gender_id')->nullable()->constrained('genders');
            $table->timestamps();
            $table->softDeletes();

            $table->index('father_id');
            $table->index('mother_id');
            $table->index('spouse_id');
            $table->index('family_no');
            $table->index('community_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_members');
    }
};
