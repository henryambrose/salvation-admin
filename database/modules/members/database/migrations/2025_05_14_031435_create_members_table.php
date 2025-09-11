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
            $table->string('old_family_no')->nullable();
            $table->string('old_sal_id')->nullable();
            $table->string('aadhar')->nullable();
            $table->string('current_family_no')->nullable();
            $table->string('family_no')->nullable();
            $table->string('member_no')->nullable();
            $table->string('registration_year')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('permanent_add1')->nullable();
            $table->string('permanent_add2')->nullable();
            $table->string('permanent_add3')->nullable();
            $table->foreignId('permanent_town_id')->nullable()->constrained('towns');
            $table->foreignId('permanent_city_id')->nullable()->constrained('cities');
            $table->foreignId('permanent_state_id')->nullable()->constrained('states');
            $table->foreignId('permanent_country_id')->nullable()->constrained('countries');
            $table->string('permanent_pincode')->nullable();
            $table->string('current_add1')->nullable();
            $table->string('current_add2')->nullable();
            $table->string('current_add3')->nullable();
            $table->foreignId('current_town_id')->nullable()->constrained('towns');
            $table->foreignId('current_city_id')->nullable()->constrained('cities');
            $table->foreignId('current_state_id')->nullable()->constrained('states');
            $table->foreignId('current_country_id')->nullable()->constrained('countries');
            $table->string('current_pincode')->nullable();
            $table->string('contact_no_1')->nullable();
            $table->string('contact_no_2')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('blood_group_id')->nullable()->constrained('blood_groups');
            $table->string('school_name')->nullable();
            $table->string('college_name')->nullable();
            $table->string('latest_qualifications')->nullable();
            $table->string('company_name')->nullable();
            $table->foreignId('income_range_id')->nullable()->constrained('income_ranges');
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
            $table->string('family_sequence')->nullable();
            $table->string('member_sequence')->nullable();
            $table->string('marital_status')->nullable();
            $table->foreignId('mother_id')->nullable()->constrained('members');
            $table->foreignId('father_id')->nullable()->constrained('members');
            $table->foreignId('spouse_id')->nullable()->constrained('members');
            $table->string('father_source')->nullable()->default('Member');
            $table->string('mother_source')->nullable()->default('Member');
            $table->string('spouse_source')->nullable()->default('Member');
            $table->foreignId('parish_id')->nullable()->constrained('parishes');
            $table->foreignId('designation_id')->nullable()->constrained('designations');
            $table->foreignId('gender_id')->nullable()->constrained('genders');
            $table->foreignId('status_id')->nullable()->constrained('statuses');
            $table->foreignId('relationship_id')->nullable()->constrained('relationships');
            $table->foreignId('baptism_parish_id')->nullable()->constrained('parishes')->onDelete('set null');
            $table->foreignId('confirmation_parish_id')->nullable()->constrained('parishes')->onDelete('set null');
            $table->foreignId('marriage_parish_id')->nullable()->constrained('parishes')->onDelete('set null');
            $table->foreignId('death_parish_id')->nullable()->constrained('parishes')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            $table->index('family_no');
            $table->index('member_no');
            $table->index('created_at');
            $table->index(['first_name', 'last_name']);
            $table->index(['community_id', 'deleted_at']);
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
