<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Members\Database\Seeders\DatabaseSeeder as MembersDatabaseSeeder;

class SeedMembersModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed:members {--fresh : Run migrations fresh before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed only the Members module database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Running migrations fresh...');
            $this->call('migrate:fresh');
        }

        $this->info('Seeding Members module...');
        
        try {
            $seeder = new MembersDatabaseSeeder();
            $seeder->setContainer($this->getLaravel());
            $seeder->setCommand($this);
            $seeder->run();
            
            $this->info('✅ Members module seeded successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Error seeding Members module: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
