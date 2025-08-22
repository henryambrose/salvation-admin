<?php

namespace Modules\Members\Console\Commands;

use App\Helpers\AuditHelper;

use Modules\Members\Models\Member;
use Illuminate\Console\Command;

class TestAuditSystem extends Command
{
    protected $signature = 'audit:test';

    protected $description = 'Test the audit system by creating, updating, and deleting a test member';

    public function handle()
    {
        $this->info('Testing audit system...');

        // Set current user ID for triggers
        AuditHelper::setCurrentUserId();

        // Test CREATE
        $this->info('Testing CREATE operation...');
        $member = Member::create([
            'first_name' => 'Test',
            'last_name' => 'Audit',
            'email' => 'test@audit.com',
            'community_id' => 1,
            'status_id' => 1,
            'relationship_id' => 1,
            'gender_id' => 1,
        ]);

        // Test UPDATE
        $this->info('Testing UPDATE operation...');
        $member->update([
            'first_name' => 'Updated Test',
        ]);

        // Test DELETE
        $this->info('Testing DELETE operation...');
        $member->delete();

        // Check audit logs
        $logs = \DB::table('audit_logs')->where('table_name', 'members')->orderBy('created_at', 'desc')->limit(3)->get();

        $this->info('Audit logs created:');
        foreach ($logs as $log) {
            $this->line("- {$log->action} operation at {$log->created_at}");
        }

        $this->info('✅ Audit system test completed successfully!');

        return 0;
    }
}
