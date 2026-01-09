<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Set Parochial Register templates as default for baptism, marriage, and death certificates.
     * Unset generic Default templates as default.
     */
    public function up(): void
    {
        // Set Parochial Register templates as default
        DB::table('certificate_templates')
            ->where('name', 'LIKE', '%Parochial Register%')
            ->update(['is_default' => true]);

        // Unset generic Default templates
        DB::table('certificate_templates')
            ->where(function ($query) {
                $query->where('name', 'Default Template')
                      ->orWhere('name', 'LIKE', 'Default %');
            })
            ->where('name', 'NOT LIKE', '%Parochial%')
            ->update(['is_default' => false]);
    }

    /**
     * Reverse the migrations.
     *
     * Restore original default settings.
     */
    public function down(): void
    {
        // Restore Default templates as default
        DB::table('certificate_templates')
            ->where(function ($query) {
                $query->where('name', 'Default Template')
                      ->orWhere('name', 'LIKE', 'Default %');
            })
            ->where('name', 'NOT LIKE', '%Parochial%')
            ->update(['is_default' => true]);

        // Unset Parochial Register templates
        DB::table('certificate_templates')
            ->where('name', 'LIKE', '%Parochial Register%')
            ->update(['is_default' => false]);
    }
};
