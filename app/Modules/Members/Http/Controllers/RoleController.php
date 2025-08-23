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
            // Extract module from permission name (e.g., "create-member" -> "Member")
            $module = $this->extractModuleFromPermission($permission->name);
            
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'module' => $module,
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
            // Extract module from permission name (e.g., "create-member" -> "Member")
            $module = $this->extractModuleFromPermission($permission->name);
            
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'module' => $module,
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

    /**
     * Extract category and module name from permission name
     */
    private function extractModuleFromPermission(string $permissionName): string
    {
        // Get category and module
        $category = $this->getCategoryForPermission($permissionName);
        $module = $this->getModuleForPermission($permissionName);
        
        return $category . ' → ' . $module;
    }

    /**
     * Get category for permission based on navigation structure
     */
    private function getCategoryForPermission(string $permissionName): string
    {
        // Dashboard
        if (str_contains($permissionName, 'dashboard')) {
            return 'Dashboard';
        }

        // Core Management
        if (str_contains($permissionName, 'member') || 
            str_contains($permissionName, 'external-member') ||
            str_contains($permissionName, 'community') ||
            str_contains($permissionName, 'parish')) {
            return 'Core Management';
        }

        // Organizational Structure
        if (str_contains($permissionName, 'zone') ||
            str_contains($permissionName, 'cluster') ||
            str_contains($permissionName, 'cells-and-association')) {
            return 'Organizational Structure';
        }

        // Leadership
        if (str_contains($permissionName, 'scc-head') ||
            str_contains($permissionName, 'ppc-head') ||
            str_contains($permissionName, 's-c-c-head') ||
            str_contains($permissionName, 'p-p-c-head')) {
            return 'Leadership';
        }

        // Member Attributes
        if (str_contains($permissionName, 'relationship') ||
            str_contains($permissionName, 'designation') ||
            str_contains($permissionName, 'age-group') ||
            str_contains($permissionName, 'blood-group') ||
            str_contains($permissionName, 'gender') ||
            str_contains($permissionName, 'status') ||
            str_contains($permissionName, 'income-range')) {
            return 'Member Attributes';
        }

        // Geographic Data
        if (str_contains($permissionName, 'country') ||
            str_contains($permissionName, 'state') ||
            str_contains($permissionName, 'city') ||
            str_contains($permissionName, 'town')) {
            return 'Geographic Data';
        }

        // System Management
        if (str_contains($permissionName, 'user') ||
            str_contains($permissionName, 'role') ||
            str_contains($permissionName, 'audit')) {
            return 'System Management';
        }

        // AI Assistance
        if (str_contains($permissionName, 'chat') ||
            str_contains($permissionName, 'ai')) {
            return 'AI Assistance';
        }

        // Fund App
        if (str_contains($permissionName, 'fund') || 
            str_contains($permissionName, 'annual-contribution') || 
            str_contains($permissionName, 'mass-intention')) {
            return 'Fund App';
        }

        // If no category matches, try to extract from permission name
        $parts = explode('-', $permissionName);
        if (count($parts) >= 2) {
            $firstPart = $parts[1];
            
            // Handle specific cases that might not have been caught
            if (str_contains($firstPart, 'group')) {
                return 'Member Attributes';
            }
            
            // For any other unrecognized permissions, group them logically
            if (str_contains($permissionName, 'create') || str_contains($permissionName, 'read') || 
                str_contains($permissionName, 'update') || str_contains($permissionName, 'delete') ||
                str_contains($permissionName, 'list') || str_contains($permissionName, 'restore')) {
                
                // Try to find a meaningful category based on the second part
                if (isset($parts[2])) {
                    $secondPart = $parts[2];
                    if (str_contains($secondPart, 'member') || str_contains($secondPart, 'user')) {
                        return 'Core Management';
                    }
                    if (str_contains($secondPart, 'zone') || str_contains($secondPart, 'cluster')) {
                        return 'Organizational Structure';
                    }
                }
            }
        }
        
        // If still no match, put in Core Management as default
        return 'Core Management';
    }

    /**
     * Get module name for permission
     */
    private function getModuleForPermission(string $permissionName): string
    {
        // Handle special cases first
        if (str_contains($permissionName, 'dashboard')) {
            return 'Dashboard';
        }
        
        if (str_contains($permissionName, 'role')) {
            return 'Role Management';
        }
        
        if (str_contains($permissionName, 'user')) {
            return 'User Management';
        }
        
        if (str_contains($permissionName, 'fund')) {
            return 'Fund Dashboard';
        }
        
        if (str_contains($permissionName, 'annual-contribution')) {
            return 'Annual Contributions';
        }
        
        if (str_contains($permissionName, 'mass-intention')) {
            return 'Mass Intentions';
        }

        // Handle specific permission patterns
        if (str_contains($permissionName, 'cells-and-association-member')) {
            return 'Cells Association Members';
        }
        
        if (str_contains($permissionName, 'cells-and-association')) {
            return 'Cells and Association';
        }
        
        // Standard module extraction
        $parts = explode('-', $permissionName);
        if (count($parts) >= 2) {
            $module = $parts[1];
            
            // Handle compound words
            if (isset($parts[2])) {
                $module .= ' ' . $parts[2];
            }
            
            // Capitalize and clean up
            $module = str_replace(['_', '-'], ' ', $module);
            $module = ucwords($module);
            
            // Handle special cases
            $moduleMapping = [
                'Member' => 'Members',
                'External Member' => 'External Members',
                'Community' => 'Community',
                'Parish' => 'Parishes',
                'Zone' => 'Zone',
                'Cluster' => 'Clusters',
                'Community Cluster' => 'Community Clusters',
                'Country' => 'Countries',
                'State' => 'State',
                'City' => 'City',
                'Town' => 'Town',
                'Age Group' => 'Age Group',
                'Blood Group' => 'Blood Group',
                'Income Range' => 'Income Range',
                'Relationship' => 'Relationship',
                'Designation' => 'Designation',
                'Gender' => 'Gender',
                'Status' => 'Status',
                'Ppc Head' => 'PPC Head',
                'Scc Head' => 'SCC Head',
                'S C C Head' => 'SCC Head',
                'P P C Head' => 'PPC Head',
            ];
            
            return $moduleMapping[$module] ?? $module;
        }
        
        // If still no match, try to create a meaningful module name
        if (count($parts) >= 2) {
            $module = $parts[1];
            if (isset($parts[2])) {
                $module .= ' ' . $parts[2];
            }
            return ucwords(str_replace(['_', '-'], ' ', $module));
        }
        
        return 'Other';
    }
}
