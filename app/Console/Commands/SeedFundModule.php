<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Fund\Database\Seeders\DatabaseSeeder as FundDatabaseSeeder;

class SeedFundModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed:fund {--fresh : Run migrations fresh before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed only the Fund module database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Running migrations fresh...');
            $this->call('migrate:fresh');
        }

        $this->info('Seeding Fund module...');
        
        try {
            $seeder = new FundDatabaseSeeder();
            $seeder->setContainer($this->getLaravel());
            $seeder->setCommand($this);
            $seeder->run();
            
            $this->info('✅ Fund module seeded successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Error seeding Fund module: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
