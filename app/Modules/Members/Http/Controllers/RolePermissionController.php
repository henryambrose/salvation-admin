<?php

namespace Modules\Members\Http\Controllers;
use App\Http\Controllers\Controller;

use Modules\Members\Models\Module;
use Modules\Members\Models\PermissionGroup;
use Modules\Members\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\PermissionCategory;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RolePermissionController extends Controller
{
    public function index()
    {
        // Fetch roles from Spatie
        $roles = Role::select(['id', 'name'])->whereNotIn('name', ['super admin'])->get();
        
        // Get modules for UI organization (keep this for now)
        $modules = Module::with('actions')->get();
        
        // Get all permissions from Spatie
        $allPermissions = Permission::all();
        
        // Get categories with their rules for database-driven grouping
        $categories = PermissionCategory::with('rules')
            ->active()
            ->ordered()
            ->get();
        
        // Group permissions by category using priority-based rules
        $permissionsByCategory = [];
        $categorizedPermissions = [];
        
        // First pass: collect all matching permissions with their categories and priorities
        foreach ($categories as $category) {
            foreach ($category->rules as $rule) {
                if (!$rule->is_active) continue;
                
                foreach ($allPermissions as $permission) {
                    $matches = false;
                    switch ($rule->rule_type) {
                        case 'contains':
                            $matches = str_contains(strtolower($permission->name), strtolower($rule->rule_value));
                            break;
                        case 'starts_with':
                            $matches = str_starts_with(strtolower($permission->name), strtolower($rule->rule_value));
                            break;
                        case 'ends_with':
                            $matches = str_ends_with(strtolower($permission->name), strtolower($rule->rule_value));
                            break;
                        case 'regex':
                            $matches = preg_match($rule->rule_value, $permission->name);
                            break;
                    }
                    
                    if ($matches) {
                        $categorizedPermissions[$permission->id] = [
                            'permission' => $permission,
                            'category' => $category->name,
                            'priority' => $rule->priority
                        ];
                    }
                }
            }
        }
        
        // Second pass: assign permissions to highest priority category
        foreach ($categorizedPermissions as $permissionId => $data) {
            $categoryName = $data['category'];
            if (!isset($permissionsByCategory[$categoryName])) {
                $permissionsByCategory[$categoryName] = [];
            }
            $permissionsByCategory[$categoryName][] = $data['permission'];
        }
        
        // Build permissions matrix for roles (keeping existing logic for now)
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
            'roles' => $roles,
            'modules' => $modules,
            'permissions' => $permissions,
            'modulesIdWise' => $modulesById,
            'permissionGroups' => $permissionGroups,
            'permissionsByCategory' => $permissionsByCategory,
            'categories' => $categories,
            'categoriesByApp' => $categories->groupBy('app'),
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

    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        DB::transaction(function () use ($request) {

            // Create the new role
            $role = Role::create([
                'name' => $request->name,

            ]);


        });

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    /**
     * Apply a permission group to a role
     */
    public function applyGroup(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'group_id' => 'required|exists:permission_groups,id',
        ]);

        $role = Role::findOrFail($request->input('role_id'));
        $permissionGroup = PermissionGroup::with('permissions')->findOrFail($request->input('group_id'));

        // Get the permission slugs from the group
        $permissionSlugs = $permissionGroup->getPermissionSlugs();

        // Convert old permission slugs to Spatie permission names
        // This is a mapping from old system to new system
        $spatiePermissions = [];
        
        foreach ($permissionSlugs as $slug) {
            // Try to find the permission in Spatie permissions table
            $spatiePermission = Permission::where('name', $slug)->first();
            if ($spatiePermission) {
                $spatiePermissions[] = $spatiePermission->name;
            }
        }

        // Sync the permissions to the role
        $role->syncPermissions($spatiePermissions);

        return redirect()->back()->with('success', "Permission group '{$permissionGroup->name}' applied to role '{$role->name}' successfully.");
    }

    /**
     * Show preview of permission group for a role.
     */
    public function preview($groupId)
    {
        $permissionGroup = PermissionGroup::findOrFail($groupId);
        
        // Get modules data
        $modules = Module::with('actions')->orderBy('name')->get();
        $modulesIdWise = $modules->keyBy('id');
        $permissionGroups = PermissionGroup::with('permissions')->orderBy('name')->get();
        
        // Get permission categories for grouping
        $categories = PermissionCategory::active()->ordered()->with('rules')->get();
        
        // Group permissions by category using priority-based rules
        $permissionsByCategory = [];
        $categorizedPermissions = [];
        
        // First pass: collect all matching permissions with their categories and priorities
        foreach ($categories as $category) {
            foreach ($category->rules as $rule) {
                if (!$rule->is_active) continue;
                
                foreach ($modules as $module) {
                    foreach ($module->actions as $action) {
                        $matches = false;
                        switch ($rule->rule_type) {
                            case 'contains':
                                $matches = str_contains(strtolower($action->slug), strtolower($rule->rule_value));
                                break;
                            case 'starts_with':
                                $matches = str_starts_with(strtolower($action->slug), strtolower($rule->rule_value));
                                break;
                            case 'ends_with':
                                $matches = str_ends_with(strtolower($action->slug), strtolower($rule->rule_value));
                                break;
                            case 'regex':
                                $matches = preg_match($rule->rule_value, $action->slug);
                                break;
                        }
                        
                        if ($matches) {
                            $categorizedPermissions[$action->id] = [
                                'permission' => $action,
                                'category' => $category->name,
                                'priority' => $rule->priority
                            ];
                        }
                    }
                }
            }
        }
        
        // Second pass: assign permissions to highest priority category
        foreach ($categorizedPermissions as $permissionId => $data) {
            $categoryName = $data['category'];
            if (!isset($permissionsByCategory[$categoryName])) {
                $permissionsByCategory[$categoryName] = [];
            }
            $permissionsByCategory[$categoryName][] = $data['permission'];
        }

        return Inertia::render('roles_permissions/Preview', [
            'modules' => $modules,
            'modulesIdWise' => $modulesIdWise,
            'permissionGroups' => $permissionGroups,
            'permissionsByCategory' => $permissionsByCategory,
            'categories' => $categories,
            'selectedGroupId' => (int) $groupId,
        ]);
    }

    /**
     * Update permissions for a specific role.
     */
    public function updatePermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
        ]);

        $role = Role::findOrFail($request->input('role_id'));
        $permissions = $request->input('permissions');

        // Get all available permissions from the modules
        $modules = Module::with('actions')->get();
        $allPermissions = [];

        // Build the list of all available permissions
        foreach ($modules as $module) {
            foreach ($module->actions as $action) {
                $allPermissions[] = $action->slug;
            }
        }

        // Get the permissions that should be assigned to this role
        $permissionsToAssign = [];
        foreach ($permissions as $moduleId => $modulePermissions) {
            foreach ($modulePermissions as $actionId => $hasPermission) {
                if ($hasPermission == 1) {
                    // Find the action slug for this action ID
                    foreach ($modules as $module) {
                        if ($module->id == $moduleId) {
                            foreach ($module->actions as $action) {
                                if ($action->id == $actionId) {
                                    $permissionsToAssign[] = $action->slug;
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Sync the permissions to the role
        $role->syncPermissions($permissionsToAssign);

        return response()->json([
            'success' => true,
            'message' => "Permissions updated successfully for role '{$role->name}'"
        ]);
    }
}
