<?php

namespace Tests\Traits;

use Modules\Members\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

trait TestsPermissions
{
    protected User $authorizedUser;
    protected User $unauthorizedUser;
    protected User $superAdmin;

    /**
     * Setup users with different permission levels
     */
    protected function setupPermissionUsers(array $permissions): void
    {
        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super admin - bypasses all checks
        $this->superAdmin = User::factory()->create(['is_superadmin' => true]);

        // Authorized user - has all permissions
        $this->authorizedUser = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'authorized-user']);
        $this->authorizedUser->assignRole($role);
        $this->authorizedUser->givePermissionTo($permissions);

        // Unauthorized user - has no permissions
        $this->unauthorizedUser = User::factory()->create();
    }

    /**
     * Test that a route requires authentication
     */
    protected function assertRequiresAuthentication(string $method, string $route, array $params = []): void
    {
        $response = match($method) {
            'GET' => $this->get(route($route, $params)),
            'POST' => $this->post(route($route, $params)),
            'PUT', 'PATCH' => $this->put(route($route, $params)),
            'DELETE' => $this->delete(route($route, $params)),
            default => throw new \InvalidArgumentException("Unsupported method: $method")
        };

        // Should redirect to login
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that a route requires specific permission
     */
    protected function assertRequiresPermission(
        string $method,
        string $route,
        array $params = [],
        array $data = []
    ): void {
        $response = $this->actingAs($this->unauthorizedUser);

        $response = match($method) {
            'GET' => $response->get(route($route, $params)),
            'POST' => $response->post(route($route, $params), $data),
            'PUT', 'PATCH' => $response->put(route($route, $params), $data),
            'DELETE' => $response->delete(route($route, $params)),
            default => throw new \InvalidArgumentException("Unsupported method: $method")
        };

        // Should return 403 Forbidden
        expect($response->status())->toBe(403);
    }

    /**
     * Test that authorized user can access route
     */
    protected function assertAuthorizedUserCanAccess(
        string $method,
        string $route,
        array $params = [],
        array $data = []
    ): void {
        $response = $this->actingAs($this->authorizedUser);

        $response = match($method) {
            'GET' => $response->get(route($route, $params)),
            'POST' => $response->post(route($route, $params), $data),
            'PUT', 'PATCH' => $response->put(route($route, $params), $data),
            'DELETE' => $response->delete(route($route, $params)),
            default => throw new \InvalidArgumentException("Unsupported method: $method")
        };

        // Should not return 403
        expect($response->status())->not->toBe(403);
    }

    /**
     * Test that super admin can access route
     */
    protected function assertSuperAdminCanAccess(
        string $method,
        string $route,
        array $params = [],
        array $data = []
    ): void {
        $response = $this->actingAs($this->superAdmin);

        $response = match($method) {
            'GET' => $response->get(route($route, $params)),
            'POST' => $response->post(route($route, $params), $data),
            'PUT', 'PATCH' => $response->put(route($route, $params), $data),
            'DELETE' => $response->delete(route($route, $params)),
            default => throw new \InvalidArgumentException("Unsupported method: $method")
        };

        // Should not return 403
        expect($response->status())->not->toBe(403);
    }
}
