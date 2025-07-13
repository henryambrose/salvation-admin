<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

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

        return inertia('roles_permissions/Index', [
            'rolesPermissions' => $roles_permissions,
            'roles' => $roles,
            'modules' => $modules,
            'permissions' => $permissions,
            'modulesIdWise' => $modulesIdWise,
        ]);
    }

    public function update(Request $request)
    {
        $roleId = $request->input('role_id');
        $permissions = $request->input('permissions');

        $role = Role::findOrFail($roleId);
        $permissionsToSync = [];
        $modules = Module::with('actions')->get(); // Or fetch from DB/config
        foreach ($modules as $module) {
            foreach ($module->actions as $action) {
                if (isset($permissions[$module->id][$action->id]) && $permissions[$module->id][$action->id]) {
                    $permissionsToSync[] = $action->slug;
                }
            }
        }
        $role->syncPermissions($permissionsToSync);

        return redirect()->back()->with('success', 'Permissions updated.');
    }
}
