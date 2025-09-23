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
        Schema::table('permanent_graves', function (Blueprint $table) {
            $table->decimal('pending_amount', 10, 2)->default(0)->after('updated_by');
            $table->integer('last_payment_year')->nullable()->after('pending_amount');
            $table->json('partial_payment_months')->nullable()->after('last_payment_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permanent_graves', function (Blueprint $table) {
            $table->dropColumn(['pending_amount', 'last_payment_year', 'partial_payment_months']);
        });
    }
};
