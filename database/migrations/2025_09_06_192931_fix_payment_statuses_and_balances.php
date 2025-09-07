<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix all existing payments with proper status and balance calculation
        DB::statement("
            UPDATE payments 
            SET 
                payment_status = CASE 
                    WHEN COALESCE(paid_amount, 0) = 0 THEN 'pending'
                    WHEN COALESCE(paid_amount, 0) >= COALESCE(total_amount, 0) THEN 'completed'
                    ELSE 'partial'
                END,
                balance_amount = COALESCE(total_amount, 0) - COALESCE(paid_amount, 0)
            WHERE payment_status IS NULL 
               OR payment_status = '' 
               OR balance_amount IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
