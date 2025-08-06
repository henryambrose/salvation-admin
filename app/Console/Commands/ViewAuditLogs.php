<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ViewAuditLogs extends Command
{
    protected $signature = 'audit:logs {--limit=10 : Number of logs to show}';
    protected $description = 'View recent audit logs';

    public function handle()
    {
        $limit = $this->option('limit');
        
        $logs = DB::table('audit_logs')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['action', 'table_name', 'record_id', 'user_id', 'created_at']);
        
        $this->info("Recent Audit Logs (last {$limit}):");
        $this->newLine();
        
        foreach ($logs as $log) {
            $this->line("• {$log->action} on {$log->table_name} (ID: {$log->record_id}) by User {$log->user_id} at {$log->created_at}");
        }
        
        return 0;
    }
} 