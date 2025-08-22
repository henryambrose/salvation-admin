<?php

use Modules\Members\Models\Cluster;
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
        // First, create a default cluster if none exists
        // if (Cluster::count() === 0) {
        //     Cluster::create([
        //         'name' => 'A',
        //     ]);
        // }

        // Get the first cluster (or create one if none exists)
        $defaultCluster = Cluster::first();

        // Check if cluster_id column already exists
        if (! Schema::hasColumn('community_clusters', 'cluster_id')) {
            Schema::table('community_clusters', function (Blueprint $table) {
                $table->foreignId('cluster_id')->after('name')->nullable()->constrained('clusters');
            });
        }

        // Update existing records to use the default cluster
        if ($defaultCluster) {
            \DB::table('community_clusters')->whereNull('cluster_id')->update(['cluster_id' => $defaultCluster->id]);
        }

        // Make the column required after updating existing data
        if (Schema::hasColumn('community_clusters', 'cluster_id')) {
            Schema::table('community_clusters', function (Blueprint $table) {
                $table->foreignId('cluster_id')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('community_clusters', 'cluster_id')) {
            Schema::table('community_clusters', function (Blueprint $table) {
                $table->dropForeign(['cluster_id']);
                $table->dropColumn('cluster_id');
            });
        }
    }
};
