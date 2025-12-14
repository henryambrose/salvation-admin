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
        Schema::create('marriage_records', function (Blueprint $table) {
            $table->id();

            // Marriage Details
            $table->date('marriage_date')->nullable();
            $table->string('marriage_reg_no')->nullable();
            $table->string('parish_of_marriage')->nullable();

            // $table->foreignId('marriage_parish_id')->nullable()->constrained('parishes')->onDelete('set null');

            // Bridegroom Information (items 2-11)
            $table->foreignId('bridegroom_member_id')->nullable()->constrained('members')->onDelete('cascade');
            $table->string('bridegroom_name')->nullable();
            $table->string('bridegroom_surname')->nullable();
            $table->date('bridegroom_dob')->nullable();
            $table->string('bridegroom_nationality')->nullable();
            $table->string('bridegroom_profession')->nullable();
            $table->text('bridegroom_residence')->nullable();
            $table->string('bridegroom_father_name')->nullable();
            $table->string('bridegroom_mother_name')->nullable();
            $table->string('bridegroom_status')->nullable(); // Bachelor or Widower
            $table->string('bridegroom_if_widower_whose')->nullable();

            // Bride Information (items 12-21)
            $table->foreignId('bride_member_id')->nullable()->constrained('members')->onDelete('cascade');
            $table->string('bride_name')->nullable();
            $table->string('bride_surname')->nullable();
            $table->date('bride_dob')->nullable();
            $table->string('bride_nationality')->nullable();
            $table->string('bride_profession')->nullable();
            $table->text('bride_residence')->nullable();
            $table->string('bride_father_name')->nullable();
            $table->string('bride_mother_name')->nullable();
            $table->string('bride_status')->nullable(); // Spinster or Widow
            $table->string('bride_if_widow_whose')->nullable();

            // Witnesses (items 22-25)
            $table->string('first_witness_name')->nullable();
            $table->text('first_witness_residence')->nullable();
            $table->string('second_witness_name')->nullable();
            $table->text('second_witness_residence')->nullable();

            // Minister and Remarks
            $table->string('minister_name')->nullable();
            $table->text('marriage_remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('bridegroom_member_id');
            $table->index('bride_member_id');
            $table->index('marriage_date');
            $table->index('marriage_reg_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marriage_records');
    }
};
