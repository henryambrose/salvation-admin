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
            $table->foreignId('permanent_town_id')->nullable()->constrained('towns');
            $table->foreignId('permanent_state_id')->nullable()->constrained('states');
            $table->foreignId('permanent_country_id')->nullable()->constrained('countries');
            $table->foreignId('current_town_id')->nullable()->constrained('towns');
            $table->foreignId('current_state_id')->nullable()->constrained('states');
            $table->foreignId('current_country_id')->nullable()->constrained('countries');
            $table->foreignId('family_income_range_id')->nullable()->constrained('family_income_ranges');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['permanent_town_id']);
            $table->dropForeign(['permanent_state_id']);
            $table->dropForeign(['permanent_country_id']);
            $table->dropForeign(['current_town_id']);
            $table->dropForeign(['current_state_id']);
            $table->dropForeign(['current_country_id']);
            $table->dropForeign(['family_income_range_id']);

            $table->dropColumn([
                'permanent_town_id',
                'permanent_state_id',
                'permanent_country_id',
                'current_town_id',
                'current_state_id',
                'current_country_id',
                'family_income_range_id'
            ]);
        });
    }
};
