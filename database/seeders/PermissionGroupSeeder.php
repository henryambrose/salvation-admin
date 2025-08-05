<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PermissionGroup;
use App\Models\ModuleAction;

class PermissionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default permission groups
        $groups = [
            [
                'name' => 'Full Access',
                'description' => 'Complete system access with all permissions',
                'is_default' => false,
            ],
            [
                'name' => 'Read Only',
                'description' => 'View data only - no create, update, delete, or restore permissions',
                'is_default' => false,
            ],
            [
                'name' => 'Data Entry',
                'description' => 'Create and edit data - no delete or restore permissions',
                'is_default' => false,
            ],
            [
                'name' => 'Administrator',
                'description' => 'Manage data but cannot delete or restore records',
                'is_default' => false,
            ],
            [
                'name' => 'Moderator',
                'description' => 'Moderate content - read, update, and restore permissions',
                'is_default' => false,
            ],
            [
                'name' => 'Custom',
                'description' => 'Manual permission selection',
                'is_default' => true,
            ],
        ];

        foreach ($groups as $groupData) {
            $group = PermissionGroup::create($groupData);
            
            // Assign permissions based on group type
            $this->assignPermissionsToGroup($group);
        }
    }

    /**
     * Assign permissions to a specific group
     */
    private function assignPermissionsToGroup(PermissionGroup $group): void
    {
        $allPermissions = ModuleAction::all();
        
        switch ($group->name) {
            case 'Full Access':
                // All permissions
                $group->permissions()->attach($allPermissions->pluck('id'));
                break;
                
            case 'Read Only':
                // Only read and list permissions
                $readPermissions = $allPermissions->filter(function ($permission) {
                    return in_array($permission->action, ['read', 'list']);
                });
                $group->permissions()->attach($readPermissions->pluck('id'));
                break;
                
            case 'Data Entry':
                // Create, read, update, list permissions (no delete or restore)
                $dataEntryPermissions = $allPermissions->filter(function ($permission) {
                    return in_array($permission->action, ['create', 'read', 'update', 'list']);
                });
                $group->permissions()->attach($dataEntryPermissions->pluck('id'));
                break;
                
            case 'Administrator':
                // All permissions except delete and restore
                $adminPermissions = $allPermissions->filter(function ($permission) {
                    return !in_array($permission->action, ['delete', 'restore']);
                });
                $group->permissions()->attach($adminPermissions->pluck('id'));
                break;
                
            case 'Moderator':
                // Read, update, restore, list permissions
                $moderatorPermissions = $allPermissions->filter(function ($permission) {
                    return in_array($permission->action, ['read', 'update', 'restore', 'list']);
                });
                $group->permissions()->attach($moderatorPermissions->pluck('id'));
                break;
                
            case 'Custom':
                // No permissions by default - user selects manually
                break;
        }
    }
}
