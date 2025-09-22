<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Members\Models\Member;
use Modules\Fund\Models\FamilyContribution;
use Modules\Graveyard\Models\ObituaryPage;
use Modules\Members\Models\AuditLog;
use Modules\Members\Models\User;

class TestAuditLogging extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'audit:test {--user-id=1 : User ID to use for testing}';

    /**
     * The console command description.
     */
    protected $description = 'Test audit logging functionality across all modules';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->option('user-id');

        // Check if user exists
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found!");
            return 1;
        }

        // Authenticate as the user for audit logging
        auth()->login($user);

        $this->info("Testing audit logging as user: {$user->name} (ID: {$user->id})");
        $this->line('');

        $initialAuditCount = AuditLog::count();
        $this->info("Initial audit log count: {$initialAuditCount}");
        $this->line('');

        // Test Members module
        $this->testMembersModule();

        // Test Fund module
        $this->testFundModule();

        // Test Graveyard module
        $this->testGraveyardModule();

        $finalAuditCount = AuditLog::count();
        $newAuditLogs = $finalAuditCount - $initialAuditCount;

        $this->line('');
        $this->info("Final audit log count: {$finalAuditCount}");
        $this->info("New audit logs created: {$newAuditLogs}");

        // Show recent audit logs
        $this->showRecentAuditLogs();

        return 0;
    }

    private function testMembersModule(): void
    {
        $this->info('Testing Members Module...');

        try {
            // Create a test member
            $member = Member::create([
                'first_name' => 'Test',
                'last_name' => 'Audit',
                'email' => 'test.audit@example.com',
                'gender_id' => 1,
                'status_id' => 1,
                'community_id' => 1,
            ]);

            $this->line("✅ Created member: {$member->first_name} {$member->last_name} (ID: {$member->id})");

            // Update the member
            $member->update(['first_name' => 'Updated Test']);
            $this->line("✅ Updated member first name");

            // Delete the member
            $member->delete();
            $this->line("✅ Deleted member");

        } catch (\Exception $e) {
            $this->error("❌ Members module test failed: " . $e->getMessage());
        }

        $this->line('');
    }

    private function testFundModule(): void
    {
        $this->info('Testing Fund Module...');

        try {
            // Create a test family contribution
            $contribution = FamilyContribution::create([
                'family_no' => 'TEST001',
                'amount' => 100.00,
                'status' => 'pending',
                'fund_category_id' => 1,
                'payment_method_id' => 1,
                'year' => 2024,
            ]);

            $this->line("✅ Created family contribution: {$contribution->family_no} - ${$contribution->amount}");

            // Update the contribution
            $contribution->update(['status' => 'paid', 'amount' => 150.00]);
            $this->line("✅ Updated contribution status and amount");

            // Delete the contribution
            $contribution->delete();
            $this->line("✅ Deleted contribution");

        } catch (\Exception $e) {
            $this->error("❌ Fund module test failed: " . $e->getMessage());
        }

        $this->line('');
    }

    private function testGraveyardModule(): void
    {
        $this->info('Testing Graveyard Module...');

        try {
            // Create a test obituary page
            $obituary = ObituaryPage::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'deceased_name' => 'Test Deceased',
                'service_type' => 'basic',
                'is_active' => true,
                'is_public' => true,
                'is_published' => false,
            ]);

            $this->line("✅ Created obituary page: {$obituary->deceased_name} (UUID: {$obituary->uuid})");

            // Update the obituary
            $obituary->update(['is_published' => true, 'service_type' => 'premium']);
            $this->line("✅ Updated obituary status and service type");

            // Delete the obituary
            $obituary->delete();
            $this->line("✅ Deleted obituary");

        } catch (\Exception $e) {
            $this->error("❌ Graveyard module test failed: " . $e->getMessage());
        }

        $this->line('');
    }

    private function showRecentAuditLogs(): void
    {
        $this->info('Recent Audit Logs:');
        $this->line('');

        $recentLogs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($recentLogs->isEmpty()) {
            $this->warn('No audit logs found.');
            return;
        }

        $this->table(
            ['ID', 'Table', 'Action', 'Record ID', 'User', 'Created At'],
            $recentLogs->map(function ($log) {
                return [
                    $log->id,
                    $log->table_name,
                    $log->action,
                    $log->record_id,
                    $log->user ? $log->user->name : 'Unknown',
                    $log->created_at->format('Y-m-d H:i:s'),
                ];
            })
        );
    }
}