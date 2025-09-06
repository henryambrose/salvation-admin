<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class  extends Migration
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
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->string('relationship', 50)->nullable();
            $table->date('death_date')->nullable();
            $table->date('burial_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->enum('grave_type', ['permanent_grave', 'niche']);
            $table->enum('member_type', ['member', 'external']);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Index for duplicate prevention queries
            $table->index(['member_id', 'deleted_at']);
            $table->index(['aadhar_no', 'deleted_at']);
        });

        // Constraints to ensure exactly one grave type is selected
        DB::statement("
            ALTER TABLE valid_members 
            ADD CONSTRAINT chk_grave_exclusivity 
            CHECK (
                (permanent_grave_id IS NOT NULL AND niche_id IS NULL) 
                OR (permanent_grave_id IS NULL AND niche_id IS NOT NULL)
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('valid_members');
    }
};
