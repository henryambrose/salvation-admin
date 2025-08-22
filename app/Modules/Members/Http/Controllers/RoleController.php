<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(): InertiaResponse
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::with(['permissions', 'users'])
            ->withCount('users')
            ->get()
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description ?? '',
                    'users_count' => $role->users_count,
                    'permissions' => $role->permissions->map(function ($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                        ];
                    }),
                    'users' => $role->users->take(5)->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'is_superadmin' => $user->is_superadmin,
                            'roles' => $user->roles->map(function ($role) {
                                return [
                                    'id' => $role->id,
                                    'name' => $role->name,
                                ];
                            }),
                        ];
                    }),
                ];
            });

        $permissions = Permission::orderBy('name')->get()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
            ];
        });

        return Inertia::render('roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function create(): InertiaResponse
    {
        $this->authorize('create', Role::class);

        $permissions = Permission::orderBy('name')->get()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
            ];
        });

        return Inertia::render('roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
            ]);

            if (!empty($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();

            return redirect()->route('roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(Role $role): InertiaResponse
    {
        $this->authorize('view', $role);

        $role->load(['permissions', 'users']);

        $roleData = [
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description ?? '',
            'permissions' => $role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            }),
            'users' => $role->users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_superadmin' => $user->is_superadmin,
                    'roles' => $user->roles->map(function ($role) {
                        return [
                            'id' => $role->id,
                            'name' => $role->name,
                        ];
                    }),
                ];
            }),
        ];

        return Inertia::render('roles/Show', [
            'role' => $roleData,
        ]);
    }

    public function edit(Role $role): InertiaResponse
    {
        $this->authorize('update', $role);

        $role->load('permissions');

        $roleData = [
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description ?? '',
            'permissions' => $role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            }),
        ];

        $permissions = Permission::orderBy('name')->get()->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
            ];
        });

        return Inertia::render('roles/Edit', [
            'role' => $roleData,
            'permissions' => $permissions,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($role->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Prevent editing system roles
        if (in_array($role->name, ['super admin', 'admin', 'viewer'])) {
            return back()->with('error', 'System roles cannot be modified.');
        }

        DB::beginTransaction();
        try {
            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
            ]);

            if (isset($validated['permissions'])) {
                $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                $role->syncPermissions($permissions);
            }

            DB::commit();

            return redirect()->route('roles.index')
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        // Prevent deleting system roles
        if (in_array($role->name, ['super admin', 'admin', 'viewer'])) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that has users assigned to it.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $validated = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        // Prevent editing system roles
        if (in_array($role->name, ['super admin', 'admin', 'viewer'])) {
            return back()->with('error', 'System roles cannot be modified.');
        }

        $permissions = Permission::whereIn('id', $validated['permissions'] ?? [])->get();
        $role->syncPermissions($permissions);

        return back()->with('success', 'Role permissions updated successfully.');
    }

    public function getUsers(Role $role)
    {
        $this->authorize('view', $role);

        $users = $role->users()->with('roles')->paginate(20);

        return response()->json($users);
    }
}
