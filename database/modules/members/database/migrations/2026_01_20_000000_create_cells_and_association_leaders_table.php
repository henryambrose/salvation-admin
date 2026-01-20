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
        Schema::create('cells_and_association_leaders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cells_and_association_id')->constrained('cells_and_associations');
            $table->foreignId('leader_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('assistant_leader_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cells_and_association_id', 'deleted_at'], 'ca_leaders_ca_id_deleted_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cells_and_association_leaders');
    }
};
