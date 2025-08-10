<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\PermissionGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index()
    {
        // Fetch roles, modules, and permissions from DB
        $roles = Role::select(['id', 'name'])->whereNotIn('name', ['superadmin'])->get();
        $modules = Module::with('actions')->get();
        $permissions = [];
        $rolesPermissions = Role::with('permissions')->get()->pluck('permissions', 'id');

        foreach ($rolesPermissions as $roleId => $rolePermissions) {
            $permissions[$roleId] = [];
            foreach ($modules as $module) {
                $moduleActionsPermission = [];
                // Check if role has permissions for this module
                foreach ($module->actions as $action) {
                    $moduleActionsPermission[$action->id] = 0;
                    foreach ($rolePermissions as $rolePermission) {
                        if ($rolePermission->name === $action->slug) {
                            $moduleActionsPermission[$action->id] = 1;
                            break;
                        }
                    }
                }
                $permissions[$roleId][$module->id] = $moduleActionsPermission;
            }
        }

        $modulesById = [];
        foreach ($modules as $module) {
            $modulesById[$module->id] = $module;
        }

        // Fetch permission groups
        $permissionGroups = PermissionGroup::with('permissions')->get();

        return inertia('roles_permissions/Index', [
            'rolesPermissions' => $rolesPermissions,
            'roles' => $roles,
            'modules' => $modules,
            'permissions' => $permissions,
            'modulesIdWise' => $modulesById,
            'permissionGroups' => $permissionGroups,
        ]);
    }

    public function update(Request $request)
    {
        $roleId = $request->input('role_id');
        $permissions = $request->input('permissions');
        $selectedGroupId = $request->input('selected_group_id');

        $role = Role::findOrFail($roleId);
        $permissionsToSync = [];

        // If a permission group is selected, use its permissions
        if ($selectedGroupId && $selectedGroupId !== 'custom') {
            $permissionGroup = PermissionGroup::with('permissions')->find($selectedGroupId);
            if ($permissionGroup) {
                $permissionsToSync = $permissionGroup->getPermissionSlugs();
            }
        } else {
            // Use manually selected permissions
            $modules = Module::with('actions')->get();
            foreach ($modules as $module) {
                $modulePermission = false;
                foreach ($module->actions as $action) {
                    if (isset($permissions[$module->id][$action->id]) && $permissions[$module->id][$action->id]) {
                        $permissionsToSync[] = $action->slug;
                        $modulePermission = true;
                    }
                }
                if ($modulePermission) {
                    $permissionsToSync[] = $module->slug; // Add module permission if any action is selected
                }
            }
        }

        $role->syncPermissions($permissionsToSync);

        return redirect()->back()->with('success', 'Permissions updated.');
    }

    public function updateUserPermissions(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);

        $user = User::findOrFail($request->user_id);
        $permissions = $request->input('permissions', []);

        // Sync individual permissions to the user
        $user->syncPermissions($permissions);

        return redirect()->back()->with('success', "Custom permissions updated for {$user->name}");
    }

    public function users()
    {
        // Load users with their direct permissions and roles
        $users = User::with(['roles', 'permissions'])->get();
        $roles = Role::select(['id', 'name'])
            ->whereNotIn('name', ['superadmin'])
            ->get();
        $modules = Module::with('actions')->get();
        
        // Add effective permissions for each user
        $users->each(function ($user) use ($modules) {
            $effectivePermissions = collect();
            
            // Get permissions from user's roles
            foreach ($user->roles as $role) {
                $rolePermissions = $role->permissions->pluck('name');
                $effectivePermissions = $effectivePermissions->merge($rolePermissions);
            }
            
            // Get user's direct permissions
            $userDirectPermissions = $user->permissions->pluck('name');
            $effectivePermissions = $effectivePermissions->merge($userDirectPermissions);
            
            // No more generic permission expansion - keep only model-based permissions
            $user->effectivePermissions = $effectivePermissions->unique()->values()->toArray();
        });
        
        return inertia('roles_permissions/Users', [
            'users' => $users,
            'roles' => $roles,
            'modules' => $modules,
        ]);
    }

    public function assignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);

        // Remove existing roles first (single role per user)
        $user->syncRoles([$role]);

        return redirect()->back()->with('success', "Role {$role->name} assigned to {$user->name}");
    }

    public function removeRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncRoles([]);

        return redirect()->back()->with('success', "All roles removed from {$user->name}");
    }
}
