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
        Schema::create('baptism_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');

            // Baptism Details
            $table->date('baptism_date')->nullable();
            $table->string('baptism_reg_no')->nullable();
            $table->string('place_of_baptism')->nullable();
            $table->foreignId('baptism_parish_id')->nullable()->constrained('parishes')->onDelete('set null');

            // Member Birth Information
            $table->string('place_of_birth')->nullable();
            $table->string('nationality')->nullable();

            // Father Information
            $table->string('father_name')->nullable();
            $table->text('father_residence')->nullable();
            $table->string('father_profession')->nullable();

            // Mother Information
            $table->string('mother_name')->nullable();

            // Godparents Information
            $table->string('godfather_name')->nullable();
            $table->text('godfather_residence')->nullable();
            $table->string('godmother_name')->nullable();
            $table->text('godmother_residence')->nullable();

            // Minister/Priest
            $table->string('minister_name')->nullable();

            // Additional Information
            $table->text('baptism_remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('member_id');
            $table->index('baptism_date');
            $table->index('baptism_reg_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('baptism_records');
    }
};
