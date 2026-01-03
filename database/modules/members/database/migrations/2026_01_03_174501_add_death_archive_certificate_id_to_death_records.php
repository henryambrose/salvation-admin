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
        Schema::table('death_records', function (Blueprint $table) {
            $table->foreignId('death_archive_certificate_id')
                ->nullable()
                ->after('id')
                ->constrained('death_archive_certificates')
                ->nullOnDelete();

            $table->index('death_archive_certificate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('death_records', function (Blueprint $table) {
            $table->dropForeign(['death_archive_certificate_id']);
            $table->dropIndex(['death_archive_certificate_id']);
            $table->dropColumn('death_archive_certificate_id');
        });
    }
};
