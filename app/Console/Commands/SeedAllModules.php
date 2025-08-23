<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Members\Database\Seeders\DatabaseSeeder as MembersDatabaseSeeder;
use Modules\Fund\Database\Seeders\DatabaseSeeder as FundDatabaseSeeder;

class SeedAllModules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed:all {--fresh : Run migrations fresh before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed all modules (Members and Fund)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Running migrations fresh...');
            $this->call('migrate:fresh');
        }

        $this->info('Seeding all modules...');
        
        try {
            // Seed Members module
            $this->info('🌱 Seeding Members module...');
            $membersSeeder = new MembersDatabaseSeeder();
            $membersSeeder->setContainer($this->getLaravel());
            $membersSeeder->setCommand($this);
            $membersSeeder->run();
            $this->info('✅ Members module seeded successfully!');
            
            // Seed Fund module
            $this->info('💰 Seeding Fund module...');
            $fundSeeder = new FundDatabaseSeeder();
            $fundSeeder->setContainer($this->getLaravel());
            $fundSeeder->setCommand($this);
            $fundSeeder->run();
            $this->info('✅ Fund module seeded successfully!');
            
            $this->info('🎉 All modules seeded successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Error seeding modules: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
