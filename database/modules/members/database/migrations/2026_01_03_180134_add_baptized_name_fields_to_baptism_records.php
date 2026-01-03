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
            $table->string('baptized_name')->nullable()->after('member_id');
            $table->string('baptized_surname')->nullable()->after('baptized_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baptism_records', function (Blueprint $table) {
            $table->dropColumn(['baptized_name', 'baptized_surname']);
        });
    }
};
