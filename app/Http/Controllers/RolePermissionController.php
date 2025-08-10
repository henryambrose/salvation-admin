<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User; // Added this import for user management

class RolePermissionController extends Controller
{
    public function index()
    {
        // Fetch roles, modules, and permissions from DB
        $roles = Role::select(['id', 'name'])->whereNotIn('name', ['superadmin'])->get();
        $modules = Module::with('actions')->get(); // Or fetch from DB/config
        $permissions = []; // Fetch permissions per role/module/action
        $roles_permissions = Role::with('permissions')->get()->pluck('permissions', 'id');

        foreach ($roles_permissions as $role_id => $role_permissions) {
            $permissions[$role_id] = [];
            foreach ($modules as $module) {
                $moduleActionsPermission = [];
                // Check if role has permissions for this module
                foreach ($module->actions as $action) {
                    $moduleActionsPermission[$action->id] = 0;
                    foreach ($role_permissions as $role_permission) {
                        if ($role_permission->name === $action->slug) {
                            $moduleActionsPermission[$action->id] = 1; // Set to 1 if permission exists
                            break;
                        }
                    }
                }
                $permissions[$role_id][$module->id] = $moduleActionsPermission;
            }
        }

        $modulesIdWise = [];
        foreach ($modules as $module) {
            $modulesIdWise[$module->id] = $module;
        }

        // Fetch permission groups
        $permissionGroups = PermissionGroup::with('permissions')->get();

        return inertia('roles_permissions/Index', [
            'rolesPermissions' => $roles_permissions,
            'roles' => $roles,
            'modules' => $modules,
            'permissions' => $permissions,
            'modulesIdWise' => $modulesIdWise,
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
        $users = User::with('roles')->get();
        $roles = Role::select(['id', 'name'])->whereNotIn('name', ['superadmin'])->get();
        $permissionGroups = PermissionGroup::all();
        $modules = Module::with('actions')->get(); // Add this line
        
        return inertia('roles_permissions/Users', [
            'users' => $users,
            'roles' => $roles,
            'permissionGroups' => $permissionGroups,
            'modules' => $modules, // Add this line
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

    public function assignRoleFromGroup(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission_group_id' => 'required|exists:permission_groups,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $permissionGroup = PermissionGroup::with('permissions')->findOrFail($request->permission_group_id);
        
        // Get the role that best matches this permission group
        $role = $this->findBestMatchingRole($permissionGroup);
        
        if ($role) {
            $user->syncRoles([$role]);
            return redirect()->back()->with('success', "User {$user->name} assigned to role {$role->name} based on {$permissionGroup->name} group");
        }
        
        return redirect()->back()->with('error', "No suitable role found for {$permissionGroup->name} group");
    }

    private function findBestMatchingRole(PermissionGroup $group): ?Role
    {
        // Map permission groups to roles based on your business logic
        $groupToRoleMap = [
            'Full Access' => 'super admin',
            'Administrator' => 'admin', 
            'Read Only' => 'viewer',
            'Data Entry' => 'admin',
            'Moderator' => 'admin',
        ];
        
        $roleName = $groupToRoleMap[$group->name] ?? 'viewer';
        return Role::where('name', $roleName)->first();
    }

    // Add this debug method temporarily
    public function debugUser($email)
    {
        $user = User::where('email', $email)->with('roles', 'permissions')->first();
        
        if (!$user) {
            return response()->json(['error' => 'User not found']);
        }
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->permissions->pluck('name'),
                'is_superadmin' => $user->is_superadmin,
            ]
        ]);
    }
}
