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
            $table->foreignId('birth_archive_certificate_id')
                ->nullable()
                ->after('member_id')
                ->constrained('birth_archive_certificates')
                ->nullOnDelete();

            $table->index('birth_archive_certificate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baptism_records', function (Blueprint $table) {
            $table->dropForeign(['birth_archive_certificate_id']);
            $table->dropIndex(['birth_archive_certificate_id']);
            $table->dropColumn('birth_archive_certificate_id');
        });
    }
};
