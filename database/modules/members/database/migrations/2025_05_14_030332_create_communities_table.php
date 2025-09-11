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
        Schema::create('communities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('zone_id')->nullable()->constrained('zones');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communities');
        Schema::table('p_p_c_heads', function (Blueprint $table) {
            $table->dropIndex(['community_id', 'deleted_at']);
        });

        Schema::table('s_c_c_heads', function (Blueprint $table) {
            $table->dropIndex(['community_id', 'deleted_at']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['first_name', 'last_name']);
            $table->dropIndex(['community_id', 'deleted_at']);
        });
    }
};
