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
        // Create family numbering sequences table
        Schema::create('church_family_numbering_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('year', 4)->nullable(); // Nullable for family group sequences
            $table->string('church_code', 3); // SAL, ABC, XYZ, etc.
            $table->unsignedInteger('last_sequence');
            $table->timestamps();
            
            $table->unique(['year', 'church_code']);
        });

        // Add family numbering fields to members table
        Schema::table('members', function (Blueprint $table) {
            $table->string('member_no', 20)->unique()->nullable()->after('family_no');
            $table->string('registration_year', 4)->nullable()->after('member_no');
            $table->string('church_code', 3)->default('SAL')->after('registration_year');
            $table->unsignedInteger('family_sequence')->nullable()->after('church_code');
            $table->unsignedInteger('member_sequence')->nullable()->after('family_sequence');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->default('single')->after('member_sequence');
            $table->string('current_family_no')->nullable()->after('family_no');
            $table->unsignedInteger('spouse_member_id')->nullable()->after('current_family_no');
            
            $table->index(['family_no', 'current_family_no']);
            $table->index(['church_code', 'registration_year']);
            $table->index('marital_status');
            $table->index('spouse_member_id');
        });

        // Create family move logs table
        Schema::create('family_move_logs', function (Blueprint $table) {
            $table->id();
            $table->string('family_no');
            $table->unsignedInteger('old_community_id')->nullable();
            $table->unsignedInteger('new_community_id')->nullable();
            $table->string('old_church_code')->nullable();
            $table->string('new_church_code')->nullable();
            $table->date('move_date');
            $table->text('reason')->nullable();
            $table->unsignedInteger('moved_by'); // User who recorded the move
            $table->timestamps();
            
            $table->index('family_no');
            $table->index('move_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_move_logs');
        
        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['family_no', 'current_family_no']);
            $table->dropIndex(['church_code', 'registration_year']);
            $table->dropIndex('marital_status');
            $table->dropIndex('spouse_member_id');
            
            $table->dropColumn([
                'member_no',
                'registration_year',
                'church_code',
                'family_sequence',
                'member_sequence',
                'marital_status',
                'current_family_no',
                'spouse_member_id'
            ]);
        });
        
        Schema::dropIfExists('church_family_numbering_sequences');
    }
};
