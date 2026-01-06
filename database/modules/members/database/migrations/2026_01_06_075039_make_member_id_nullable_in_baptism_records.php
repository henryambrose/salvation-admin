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
        Schema::table('baptism_records', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['member_id']);

            // Make member_id nullable
            $table->foreignId('member_id')->nullable()->change();

            // Re-add foreign key with nullable support
            $table->foreign('member_id')
                ->references('id')
                ->on('members')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baptism_records', function (Blueprint $table) {
            // Drop the nullable foreign key
            $table->dropForeign(['member_id']);

            // Make member_id not nullable again
            $table->foreignId('member_id')->nullable(false)->change();

            // Re-add foreign key with cascade delete
            $table->foreign('member_id')
                ->references('id')
                ->on('members')
                ->onDelete('cascade');
        });
    }
};
