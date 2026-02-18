<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('niche_transfers') && !Schema::hasTable('remains_transfers')) {
            Schema::rename('niche_transfers', 'remains_transfers');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('remains_transfers') && !Schema::hasTable('niche_transfers')) {
            Schema::rename('remains_transfers', 'niche_transfers');
        }
    }
};
