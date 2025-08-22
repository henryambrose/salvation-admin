<?php

namespace Modules\Members\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Members\Models\PermissionGroup;
use Modules\Members\Models\ModuleAction;

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
                // All permissions including role management
                $group->permissions()->attach($allPermissions->pluck('id'));
                break;
                
            case 'Read Only':
                // Only read and list permissions, excluding role management, but including dashboard
                $readPermissions = $allPermissions->filter(function ($permission) {
                    return (in_array($permission->action, ['read', 'list']) && 
                           !$this->isRoleManagementPermission($permission)) ||
                           $this->isDashboardPermission($permission);
                });
                $group->permissions()->attach($readPermissions->pluck('id'));
                break;
                
            case 'Data Entry':
                // Create, read, update, list permissions (no delete or restore), excluding role management, but including dashboard
                $dataEntryPermissions = $allPermissions->filter(function ($permission) {
                    return (in_array($permission->action, ['create', 'read', 'update', 'list']) && 
                           !$this->isRoleManagementPermission($permission)) ||
                           $this->isDashboardPermission($permission);
                });
                $group->permissions()->attach($dataEntryPermissions->pluck('id'));
                break;
                
            case 'Administrator':
                // All permissions except delete and restore, excluding role management, but including dashboard
                $adminPermissions = $allPermissions->filter(function ($permission) {
                    return (!in_array($permission->action, ['delete', 'restore']) && 
                           !$this->isRoleManagementPermission($permission)) ||
                           $this->isDashboardPermission($permission);
                });
                $group->permissions()->attach($adminPermissions->pluck('id'));
                break;
                
            case 'Moderator':
                // Read, update, restore, list permissions, excluding role management, but including dashboard
                $moderatorPermissions = $allPermissions->filter(function ($permission) {
                    return (in_array($permission->action, ['read', 'update', 'restore', 'list']) && 
                           !$this->isRoleManagementPermission($permission)) ||
                           $this->isDashboardPermission($permission);
                });
                $group->permissions()->attach($moderatorPermissions->pluck('id'));
                break;
                
            case 'Custom':
                // Only dashboard permissions by default
                $dashboardPermissions = $allPermissions->filter(function ($permission) {
                    return $this->isDashboardPermission($permission);
                });
                $group->permissions()->attach($dashboardPermissions->pluck('id'));
                break;
        }
    }

    /**
     * Check if a permission is related to role management
     */
    private function isRoleManagementPermission($permission): bool
    {
        // Check if the permission is related to role management
        // This could be based on module name, action, or slug
        $roleManagementKeywords = ['role', 'permission', 'manage-roles', 'manage-permissions'];
        
        // Check module name
        if (isset($permission->module) && $permission->module) {
            $moduleName = strtolower($permission->module->name);
            foreach ($roleManagementKeywords as $keyword) {
                if (str_contains($moduleName, $keyword)) {
                    return true;
                }
            }
        }
        
        // Check action slug
        if (isset($permission->slug)) {
            $slug = strtolower($permission->slug);
            foreach ($roleManagementKeywords as $keyword) {
                if (str_contains($slug, $keyword)) {
                    return true;
                }
            }
        }
        
        // Check action name
        if (isset($permission->action)) {
            $action = strtolower($permission->action);
            foreach ($roleManagementKeywords as $keyword) {
                if (str_contains($action, $keyword)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Check if a permission is related to dashboard access
     */
    private function isDashboardPermission($permission): bool
    {
        // Check if the permission is related to dashboard
        $dashboardKeywords = ['dashboard', 'home', 'main'];
        
        // Check module name
        if (isset($permission->module) && $permission->module) {
            $moduleName = strtolower($permission->module->name);
            foreach ($dashboardKeywords as $keyword) {
                if (str_contains($moduleName, $keyword)) {
                    return true;
                }
            }
        }
        
        // Check action slug
        if (isset($permission->slug)) {
            $slug = strtolower($permission->slug);
            foreach ($dashboardKeywords as $keyword) {
                if (str_contains($slug, $keyword)) {
                    return true;
                }
            }
        }
        
        // Check action name
        if (isset($permission->action)) {
            $action = strtolower($permission->action);
            foreach ($dashboardKeywords as $keyword) {
                if (str_contains($action, $keyword)) {
                    return true;
                }
            }
        }
        
        return false;
    }
}
