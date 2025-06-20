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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->nullable()->constrained('communities');
            $table->foreignId('community_cluster_id')->nullable()->constrained('community_clusters');
            $table->string('new_olsc_id')->nullable();
            $table->string('old_sal_id')->nullable();
            $table->string('aadhar')->nullable();
            $table->string('family_no')->nullable();
            $table->string('last_name')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('permanent_add1')->nullable();
            $table->string('permanent_add2')->nullable();
            $table->string('permanent_add3')->nullable();
            $table->string('permanent_town')->nullable();
            $table->string('permanent_city')->nullable();
            $table->string('permanent_pincode')->nullable();
            $table->string('permanent_state')->nullable();
            $table->string('permanent_country')->nullable();
            $table->string('current_add1')->nullable();
            $table->string('current_add2')->nullable();
            $table->string('current_add3')->nullable();
            $table->string('current_town')->nullable();
            $table->string('current_city')->nullable();
            $table->string('current_pincode')->nullable();
            $table->string('current_state')->nullable();
            $table->string('current_country')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('blood_group_id')->nullable()->constrained('blood_groups');
            $table->foreignId('cells_and_association_id')->nullable()->constrained('cells_and_associations');
            $table->string('school_name')->nullable();
            $table->string('college_name')->nullable();
            $table->string('latest_qualifications')->nullable();
            $table->string('company_name')->nullable();
            $table->string('family_income_range')->nullable();
            $table->date('baptism_date')->nullable();
            $table->string('baptism_reg_no')->nullable();
            $table->string('baptism_parish')->nullable();
            $table->date('confirmation_date')->nullable();
            $table->string('confirmation_reg_no')->nullable();
            $table->string('confirmation_parish')->nullable();
            $table->date('marriage_date')->nullable();
            $table->string('marriage_reg_no')->nullable();
            $table->string('marriage_parish')->nullable();
            $table->date('death_date')->nullable();
            $table->string('deaths_reg_no')->nullable();
            $table->string('death_parish')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
