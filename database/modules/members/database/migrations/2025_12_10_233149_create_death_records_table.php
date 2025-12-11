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
        Schema::create('death_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('cascade');

            // Death and Burial Details
            $table->date('death_date')->nullable();
            $table->date('burial_date')->nullable();
            $table->string('burial_reg_no')->nullable();
            $table->foreignId('burial_parish_id')->nullable()->constrained('parishes')->onDelete('set null');

            // Deceased Information
            $table->string('deceased_name')->nullable();
            $table->string('deceased_surname')->nullable();
            $table->string('relationship')->nullable(); // w/o (wife of), h/o (husband of), s/o (son of), d/o (daughter of), etc.
            $table->text('residence')->nullable();
            $table->integer('age')->nullable();
            $table->string('nationality')->nullable();

            // Death Details
            $table->string('cause_of_death')->nullable();
            $table->string('place_of_burial')->nullable(); // Cemetery name/number
            $table->string('minister_name')->nullable();
            $table->text('death_remarks')->nullable(); // SDR number or other remarks

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('member_id');
            $table->index('death_date');
            $table->index('burial_date');
            $table->index('burial_reg_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('death_records');
    }
};
