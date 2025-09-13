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
        Schema::table('obituary_pages', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('hobbies_interests')
                ->comment('Family thoughts, funeral mass details, months mind mass timing and place, condolence messages from family etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obituary_pages', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
