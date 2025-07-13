<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function index()
    {
        // Fetch roles, modules, and permissions from DB
        $roles = Role::all(['id', 'name']);
        $modules = ['Member', 'Community']; // Or fetch from DB/config
        $permissions = []; // Fetch permissions per role/module/action

        foreach ($roles as $role) {
            $permissions[$role->id] = []; // e.g. ['Member' => ['create'=>1, ...], ...]
            foreach ($modules as $module) {
                $permissions[$role->id][$module] = [
                    'create' => 0,
                    'read' => 0,
                    'update' => 0,
                    'delete' => 0,
                    'list' => 0,
                ];
                // Fill with actual permission values from DB
                // Example: $perm = ...; $permissions[$role->id][$module]['create'] = $perm->create;
            }
        }

        return inertia('roles_permissions/Index', [
            'roles' => $roles,
            'modules' => $modules,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request)
    {
        $roleId = $request->input('role_id');
        $permissions = $request->input('permissions');

        // Save permissions to DB for the given role
        // Example: foreach ($permissions as $module => $actions) { ... }

        return redirect()->back()->with('success', 'Permissions updated.');
    }
}
