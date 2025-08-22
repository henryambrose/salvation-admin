<?php

namespace Modules\Members\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupExternalMemberPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'external-member:setup-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup external member permissions and assign to roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up external member permissions...');

        // Run the migration
        $this->info('Running migration...');
        Artisan::call('migrate', ['--path' => 'database/migrations/2025_01_XX_XXXXXX_add_external_member_permissions.php']);

        // Run the seeder
        $this->info('Running seeder...');
        Artisan::call('db:seed', ['--class' => 'ExternalMemberPermissionSeeder']);

        $this->info('External member permissions setup completed successfully!');

        return 0;
    }
}
