<?php

namespace App\Console\Commands;

use App\Models\Parish;
use Illuminate\Console\Command;

class CheckParishes extends Command
{
    protected $signature = 'parishes:check';
    protected $description = 'Check what parishes exist in the database';

    public function handle()
    {
        $parishes = Parish::all();
        
        if ($parishes->count() === 0) {
            $this->info('No parishes found in the database.');
            return;
        }
        
        $this->info('Parishes in database:');
        $this->table(
            ['ID', 'Name', 'Deanery', 'Code'],
            $parishes->map(function ($parish) {
                return [
                    $parish->id,
                    $parish->name,
                    $parish->deanery,
                    $parish->code
                ];
            })->toArray()
        );
    }
}
