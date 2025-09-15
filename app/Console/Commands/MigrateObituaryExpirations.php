<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Graveyard\Models\ObituaryPage;
use Carbon\Carbon;

class MigrateObituaryExpirations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'obituary:migrate-expirations
                            {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing obituaries to have expiration dates based on their service type';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $obituariesWithoutExpiry = ObituaryPage::whereNull('expires_at')->get();

        if ($obituariesWithoutExpiry->isEmpty()) {
            $this->info('All obituaries already have expiration dates.');
            return 0;
        }

        $this->info("Found {$obituariesWithoutExpiry->count()} obituaries without expiration dates.");

        $updated = 0;
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }

        foreach ($obituariesWithoutExpiry as $obituary) {
            $serviceType = $obituary->service_type;
            $days = config("obituary.duration.{$serviceType}");

            if (!$days) {
                $this->error("Unknown service type: {$serviceType}");
                continue;
            }

            $expirationDate = $obituary->created_at->addDays($days);

            $this->line("UUID: {$obituary->uuid}");
            $this->line("  Service: {$serviceType} ({$days} days)");
            $this->line("  Created: {$obituary->created_at->format('Y-m-d')}");
            $this->line("  New Expiry: {$expirationDate->format('Y-m-d')}");

            if (!$dryRun) {
                $obituary->update(['expires_at' => $expirationDate]);
                $this->line("  ✅ Updated");
            } else {
                $this->line("  🔍 Would update");
            }

            $updated++;
            $this->line('');
        }

        if ($dryRun) {
            $this->info("DRY RUN: Would update {$updated} obituaries.");
            $this->info('Run without --dry-run to apply changes.');
        } else {
            $this->info("Successfully updated {$updated} obituaries with expiration dates.");
        }

        return 0;
    }
}
