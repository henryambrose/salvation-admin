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
        Schema::table('obituary_condolences', function (Blueprint $table) {
            $table->string('visitor_phone', 20)->nullable()->after('visitor_email');
            $table->string('relationship', 50)->nullable()->after('visitor_phone');
            $table->string('visitor_ip', 45)->nullable()->after('message');
            $table->text('user_agent')->nullable()->after('visitor_ip');
            $table->timestamp('submitted_at')->nullable()->after('user_agent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obituary_condolences', function (Blueprint $table) {
            $table->dropColumn([
                'visitor_phone',
                'relationship', 
                'visitor_ip',
                'user_agent',
                'submitted_at'
            ]);
        });
    }
};
