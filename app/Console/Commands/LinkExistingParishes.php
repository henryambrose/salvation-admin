<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Models\Parish;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LinkExistingParishes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parishes:link-existing {--dry-run : Show what would be done without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Link existing parish names in members table to Parish table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $parishFields = ['baptism_parish', 'confirmation_parish', 'marriage_parish', 'death_parish'];
        $stats = [
            'total_processed' => 0,
            'linked' => 0,
            'kept_as_custom' => 0,
            'new_parishes_created' => 0
        ];

        foreach ($parishFields as $field) {
            $this->info("\nProcessing {$field}...");
            
            $members = Member::whereNotNull($field)
                ->where($field, '!=', '')
                ->whereNull($field . '_id')
                ->get();

            $this->info("Found {$members->count()} members with {$field} data");

            foreach ($members as $member) {
                $parishName = trim($member->$field);
                if (empty($parishName)) continue;

                $stats['total_processed']++;

                // First, check if the value is a numeric ID
                if (is_numeric($parishName)) {
                    $parish = Parish::find($parishName);
                } else {
                    // Try to find exact match (case-insensitive)
                    $parish = Parish::whereRaw('LOWER(name) = ?', [Str::lower($parishName)])->first();
                }

                if ($parish) {
                    // Link to existing parish
                    if (!$isDryRun) {
                        $member->{$field . '_id'} = $parish->id;
                        $member->$field = null; // Clear the string field
                        $member->save();
                    }
                    $stats['linked']++;
                    $this->line("✓ Linked '{$parishName}' to existing parish: {$parish->name}");
                } else {
                    // Check if we should create a new parish
                    $this->warn("⚠ Parish '{$parishName}' not found in Parish table");
                    
                    if (!$isDryRun && $this->confirm("Create new parish '{$parishName}'?")) {
                        $newParish = Parish::create([
                            'name' => $parishName,
                            'deanery' => 'Unknown',
                            'code' => Str::upper(Str::slug($parishName, ''))
                        ]);
                        
                        $member->{$field . '_id'} = $newParish->id;
                        $member->$field = null;
                        $member->save();
                        
                        $stats['new_parishes_created']++;
                        $this->info("✓ Created new parish: {$newParish->name}");
                    } else {
                        $stats['kept_as_custom']++;
                        $this->line("  Kept as custom parish name");
                    }
                }
            }
        }

        // Summary
        $this->newLine();
        $this->info('=== MIGRATION SUMMARY ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Processed', $stats['total_processed']],
                ['Linked to Existing', $stats['linked']],
                ['Kept as Custom', $stats['kept_as_custom']],
                ['New Parishes Created', $stats['new_parishes_created']],
            ]
        );

        if ($isDryRun) {
            $this->warn('This was a dry run. Run without --dry-run to apply changes.');
        } else {
            $this->info('Parish linking completed successfully!');
        }

        return 0;
    }
}
