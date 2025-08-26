<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PermissionCategory;
use App\Models\PermissionCategoryRule;

class CleanupFundAppManagementCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:cleanup-fund-app-management';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove Fund App Management category and its rules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up Fund App Management category...');

        // Find the Fund App Management category
        $fundAppCategory = PermissionCategory::where('name', 'Fund App Management')
            ->orWhere('name', 'Fund App')
            ->orWhere('slug', 'fund-app-management')
            ->orWhere('slug', 'fund')
            ->first();

        if ($fundAppCategory) {
            $this->info("Found Fund App Management category: {$fundAppCategory->name} (ID: {$fundAppCategory->id})");
            
            // Remove all rules for this category
            $rulesCount = PermissionCategoryRule::where('permission_category_id', $fundAppCategory->id)->count();
            PermissionCategoryRule::where('permission_category_id', $fundAppCategory->id)->delete();
            $this->info("Removed {$rulesCount} rules for Fund App Management category");
            
            // Remove the category itself
            $fundAppCategory->delete();
            $this->info("Successfully removed Fund App Management category");
        } else {
            $this->info('No Fund App Management category found to remove');
        }

        $this->info('Cleanup completed successfully!');
        
        return Command::SUCCESS;
    }
}
