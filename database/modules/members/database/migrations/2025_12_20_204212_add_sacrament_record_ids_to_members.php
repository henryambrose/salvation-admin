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
        Schema::table('members', function (Blueprint $table) {
            // Add foreign key columns
            $table->unsignedBigInteger('baptismrecord_id')->nullable()->after('baptism_parish_id');
            $table->unsignedBigInteger('marriagerecord_id')->nullable()->after('marriage_parish_id');
            $table->unsignedBigInteger('deathrecord_id')->nullable()->after('death_parish_id');

            // Add foreign key constraints
            $table->foreign('baptismrecord_id')
                  ->references('id')->on('baptism_records')
                  ->onDelete('set null');

            $table->foreign('marriagerecord_id')
                  ->references('id')->on('marriage_records')
                  ->onDelete('set null');

            $table->foreign('deathrecord_id')
                  ->references('id')->on('death_records')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['baptismrecord_id']);
            $table->dropForeign(['marriagerecord_id']);
            $table->dropForeign(['deathrecord_id']);

            $table->dropColumn(['baptismrecord_id', 'marriagerecord_id', 'deathrecord_id']);
        });
    }
};
