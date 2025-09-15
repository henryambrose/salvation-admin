<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Graveyard\Models\ObituaryPage;
use Carbon\Carbon;

class ManageObituaryExpiration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'obituary:manage-expiration
                            {--list : List obituaries expiring soon}
                            {--expire : Process expired obituaries}
                            {--extend=30 : Extend expiration by specified days}
                            {--uuid= : Target specific obituary UUID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage obituary expiration dates - list, process expired, or extend obituaries';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('list')) {
            return $this->listExpiring();
        }

        if ($this->option('expire')) {
            return $this->processExpired();
        }

        if ($this->option('extend')) {
            return $this->extendExpiration();
        }

        $this->info('Use --list, --expire, or --extend options. See help for details.');
        return 0;
    }

    private function listExpiring()
    {
        $now = Carbon::now();
        $soon = $now->copy()->addDays(7); // Next 7 days

        $expiring = ObituaryPage::whereNotNull('expires_at')
            ->whereBetween('expires_at', [$now, $soon])
            ->with(['permanentGraveBooking.validMember', 'temporaryGraveBooking'])
            ->get();

        if ($expiring->isEmpty()) {
            $this->info('No obituaries expiring in the next 7 days.');
            return 0;
        }

        $this->table([
            'UUID',
            'Deceased Name',
            'Service Type',
            'Expires At',
            'Days Until Expiry'
        ], $expiring->map(function ($obituary) use ($now) {
            return [
                substr($obituary->uuid, 0, 8) . '...',
                $obituary->deceased_name,
                $obituary->service_type,
                $obituary->expires_at?->format('Y-m-d H:i'),
                $obituary->expires_at ? $now->diffInDays($obituary->expires_at, false) . ' days' : 'Never',
            ];
        }));

        return 0;
    }

    private function processExpired()
    {
        $expired = ObituaryPage::where('expires_at', '<', Carbon::now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No expired obituaries found.');
            return 0;
        }

        $this->info("Found {$expired->count()} expired obituaries to process.");

        $processed = 0;
        $gracePeriodDays = config('obituary.grace_period_days', 30);
        $graceCutoff = Carbon::now()->subDays($gracePeriodDays);

        foreach ($expired as $obituary) {
            $action = '';

            // If beyond grace period, deactivate completely
            if ($obituary->expires_at < $graceCutoff) {
                $obituary->update([
                    'is_active' => false,
                    'is_public' => false,
                ]);
                $action = 'Deactivated (beyond grace period)';
            }
            // Within grace period for premium - downgrade to basic
            elseif ($obituary->service_type === 'premium') {
                $basicDays = config('obituary.duration.basic', 90);
                $obituary->update([
                    'service_type' => 'basic',
                    'expires_at' => Carbon::now()->addDays($basicDays),
                ]);
                $action = "Downgraded to basic service (expires in {$basicDays} days)";
            }
            // Basic service expired - deactivate
            else {
                $obituary->update([
                    'is_active' => false,
                ]);
                $action = 'Deactivated basic service';
            }

            $this->line("✓ {$obituary->deceased_name}: {$action}");
            $processed++;
        }

        $this->info("Successfully processed {$processed} expired obituaries.");
        return 0;
    }

    private function extendExpiration()
    {
        $days = (int) $this->option('extend');
        $uuid = $this->option('uuid');

        if ($uuid) {
            $obituary = ObituaryPage::where('uuid', $uuid)->first();
            if (!$obituary) {
                $this->error("Obituary with UUID {$uuid} not found.");
                return 1;
            }

            $obituaries = collect([$obituary]);
        } else {
            // Extend all obituaries that are expiring in the next 7 days
            $soon = Carbon::now()->addDays(7);
            $obituaries = ObituaryPage::whereNotNull('expires_at')
                ->where('expires_at', '<=', $soon)
                ->get();
        }

        if ($obituaries->isEmpty()) {
            $this->info('No obituaries found to extend.');
            return 0;
        }

        $extended = 0;
        foreach ($obituaries as $obituary) {
            $oldExpiry = $obituary->expires_at;
            $newExpiry = ($oldExpiry ?? Carbon::now())->addDays($days);

            $obituary->update(['expires_at' => $newExpiry]);

            $this->line("✓ Extended {$obituary->deceased_name} expiry to {$newExpiry->format('Y-m-d')}");
            $extended++;
        }

        $this->info("Successfully extended {$extended} obituaries by {$days} days.");
        return 0;
    }
}
