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
        Schema::table('mass_intentions', function (Blueprint $table) {
            $table->string('transaction_reference')->nullable()->after('payment_method_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mass_intentions', function (Blueprint $table) {
            $table->dropColumn('transaction_reference');
        });
    }
};
