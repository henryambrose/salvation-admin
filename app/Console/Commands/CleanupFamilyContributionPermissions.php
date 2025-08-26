<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CleanupFamilyContributionPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:cleanup-family-contribution';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove family_contribution permissions and clean up roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cleaning up family_contribution permissions...');

        // Find all family_contribution permissions
        $familyContributionPermissions = Permission::where('name', 'like', '%family-contribution%')
            ->orWhere('name', 'like', '%family_contribution%')
            ->get();

        if ($familyContributionPermissions->count() > 0) {
            $this->info("Found {$familyContributionPermissions->count()} family_contribution permissions to remove:");
            
            foreach ($familyContributionPermissions as $permission) {
                $this->line("- {$permission->name}");
            }

            // Remove these permissions from all roles
            $roles = Role::all();
            foreach ($roles as $role) {
                $role->revokePermissionTo($familyContributionPermissions);
                $this->info("Removed family_contribution permissions from role: {$role->name}");
            }

            // Delete the permissions
            $deletedCount = $familyContributionPermissions->count();
            Permission::whereIn('id', $familyContributionPermissions->pluck('id'))->delete();
            
            $this->info("Successfully removed {$deletedCount} family_contribution permissions");
        } else {
            $this->info('No family_contribution permissions found to remove');
        }

        $this->info('Cleanup completed successfully!');
        
        return Command::SUCCESS;
    }
}
