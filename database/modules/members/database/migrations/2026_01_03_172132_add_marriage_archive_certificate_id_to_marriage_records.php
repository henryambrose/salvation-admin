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
        Schema::table('marriage_records', function (Blueprint $table) {
            $table->foreignId('marriage_archive_certificate_id')
                ->nullable()
                ->after('id')
                ->constrained('marriage_archive_certificates')
                ->nullOnDelete();

            $table->index('marriage_archive_certificate_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marriage_records', function (Blueprint $table) {
            $table->dropForeign(['marriage_archive_certificate_id']);
            $table->dropIndex(['marriage_archive_certificate_id']);
            $table->dropColumn('marriage_archive_certificate_id');
        });
    }
};
