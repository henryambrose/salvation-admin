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
            $table->string('birth_text')->nullable()->after('baptism_date');
            $table->string('reg_year')->nullable()->after('birth_text');
            $table->string('confirmation_text')->nullable()->after('confirmation_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baptism_records', function (Blueprint $table) {
            $table->dropColumn(['birth_text', 'reg_year', 'confirmation_text']);
        });
    }
};
