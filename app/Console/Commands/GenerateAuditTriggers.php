<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GenerateAuditTriggers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'audit:generate-triggers {table : The table name to generate triggers for}';

    /**
     * The console command description.
     */
    protected $description = 'Generate audit triggers for a specific table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tableName = $this->argument('table');

        if (! Schema::hasTable($tableName)) {
            $this->error("Table '{$tableName}' does not exist!");

            return 1;
        }

        $this->info("Generating audit triggers for table: {$tableName}");

        try {
            $this->createTriggers($tableName);
            $this->info("✅ Audit triggers created successfully for table: {$tableName}");

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to create triggers: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Create triggers for the specified table
     */
    private function createTriggers(string $tableName): void
    {
        // Get table columns for JSON_OBJECT
        $columns = Schema::getColumnListing($tableName);
        $jsonColumns = [];

        foreach ($columns as $column) {
            $jsonColumns[] = "'{$column}', NEW.{$column}";
        }

        $jsonObject = 'JSON_OBJECT('.implode(', ', $jsonColumns).')';
        $oldJsonObject = str_replace('NEW.', 'OLD.', $jsonObject);

        // Create INSERT trigger
        DB::unprepared("
            DROP TRIGGER IF EXISTS {$tableName}_audit_insert;
            CREATE TRIGGER {$tableName}_audit_insert
            AFTER INSERT ON {$tableName}
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (
                    table_name, action, record_id, new_values, user_id, created_at, updated_at
                ) VALUES (
                    '{$tableName}',
                    'CREATE',
                    NEW.id,
                    {$jsonObject},
                    (SELECT user_id FROM sessions WHERE id = CONNECTION_ID()),
                    NOW(),
                    NOW()
                );
            END;
        ");

        // Create UPDATE trigger
        DB::unprepared("
            DROP TRIGGER IF EXISTS {$tableName}_audit_update;
            CREATE TRIGGER {$tableName}_audit_update
            AFTER UPDATE ON {$tableName}
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (
                    table_name, action, record_id, old_values, new_values, user_id, created_at, updated_at
                ) VALUES (
                    '{$tableName}',
                    'UPDATE',
                    NEW.id,
                    {$oldJsonObject},
                    {$jsonObject},
                    (SELECT user_id FROM sessions WHERE id = CONNECTION_ID()),
                    NOW(),
                    NOW()
                );
            END;
        ");

        // Create DELETE trigger
        DB::unprepared("
            DROP TRIGGER IF EXISTS {$tableName}_audit_delete;
            CREATE TRIGGER {$tableName}_audit_delete
            AFTER DELETE ON {$tableName}
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_logs (
                    table_name, action, record_id, old_values, user_id, created_at, updated_at
                ) VALUES (
                    '{$tableName}',
                    'DELETE',
                    OLD.id,
                    {$oldJsonObject},
                    (SELECT user_id FROM sessions WHERE id = CONNECTION_ID()),
                    NOW(),
                    NOW()
                );
            END;
        ");
    }
}
