<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DropAuditTriggers extends Command
{
    protected $signature = 'audit:drop-triggers';
    protected $description = 'Drop audit triggers to fix SQL errors';

    public function handle()
    {
        $this->info('Dropping audit triggers...');
        
        try {
            DB::unprepared('DROP TRIGGER IF EXISTS members_audit_insert');
            DB::unprepared('DROP TRIGGER IF EXISTS members_audit_update');
            DB::unprepared('DROP TRIGGER IF EXISTS members_audit_delete');
            
            $this->info('✅ Audit triggers dropped successfully!');
            $this->info('You can now update member records without SQL errors.');
            $this->info('To re-enable audit logging later, run: php artisan audit:generate-triggers members');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error dropping triggers: ' . $e->getMessage());
            return 1;
        }
    }
} 