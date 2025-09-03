<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateValidMembersTable extends Migration
{
    public function up()
    {
        Schema::create('valid_members', function (Blueprint $table) {
            $table->id();
            
            // Separate FKs for grave types - exactly one must be non-null
            $table->foreignId('permanent_grave_id')->nullable()
                  ->constrained('permanent_graves')
                  ->onDelete('cascade');
            $table->foreignId('niche_id')->nullable()
                  ->constrained('niches')
                  ->onDelete('cascade');
            
            // Member information - either parish member OR external
            $table->foreignId('member_id')->nullable()
                  ->constrained('members')
                  ->onDelete('cascade');
            
            // External member fields (for non-parish members)
            $table->string('first_name');
            $table->string('last_name');
            $table->string('contact_no')->nullable();
            $table->string('aadhar_no')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index for duplicate prevention queries
            $table->index(['member_id', 'deleted_at']);
            $table->index(['aadhar_no', 'deleted_at']);
        });

        // Add check constraint using raw SQL
        DB::statement('ALTER TABLE valid_members ADD CONSTRAINT chk_grave_type CHECK ((permanent_grave_id IS NOT NULL AND niche_id IS NULL) OR (permanent_grave_id IS NULL AND niche_id IS NOT NULL))');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('valid_members');
    }
}
