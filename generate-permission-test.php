<?php

/**
 * Permission Test Generator
 *
 * Usage: php generate-permission-test.php ControllerName [module]
 * Example: php generate-permission-test.php Parish members
 */

if ($argc < 2) {
    echo "Usage: php generate-permission-test.php ResourceName [module]\n";
    echo "Example: php generate-permission-test.php Parish members\n";
    echo "Example: php generate-permission-test.php MassIntention fund\n";
    exit(1);
}

$resourceName = $argv[1];
$module = $argv[2] ?? 'members';
$moduleTitleCase = ucfirst($module);

// Generate permission names
$permissionPrefix = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $resourceName));
$tableName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $resourceName)) . 's';
$routePrefix = strtolower($resourceName);

// Determine model namespace
$modelNamespaces = [
    'members' => 'Modules\Members\Models',
    'fund' => 'Modules\Fund\Models',
    'graveyard' => 'Modules\Graveyard\Models',
];
$modelNamespace = $modelNamespaces[$module] ?? 'Modules\Members\Models';

// Determine route prefix
$routePrefixes = [
    'members' => '',
    'fund' => 'fund.',
    'graveyard' => 'graveyard.',
];
$routeBase = $routePrefixes[$module];

$testContent = <<<PHP
<?php

use Tests\Traits\TestsPermissions;
use {$modelNamespace}\\{$resourceName};

uses(TestsPermissions::class);

beforeEach(function () {
    \$this->setupPermissionUsers([
        'list-{$permissionPrefix}',
        'create-{$permissionPrefix}',
        'read-{$permissionPrefix}',
        'update-{$permissionPrefix}',
        'delete-{$permissionPrefix}',
    ]);
});

describe('{$resourceName} CRUD Permissions', function () {

    test('LIST: requires authentication', function () {
        \$this->assertRequiresAuthentication('GET', '{$routeBase}{$routePrefix}.index');
    });

    test('LIST: unauthorized users are denied', function () {
        \$this->assertRequiresPermission('GET', '{$routeBase}{$routePrefix}.index');
    });

    test('LIST: authorized users can access', function () {
        \$this->assertAuthorizedUserCanAccess('GET', '{$routeBase}{$routePrefix}.index');
    });

    test('LIST: super admin can access', function () {
        \$this->assertSuperAdminCanAccess('GET', '{$routeBase}{$routePrefix}.index');
    });

    test('CREATE: unauthorized users are denied', function () {
        // TODO: Add required fields for {$resourceName}
        \$this->assertRequiresPermission('POST', '{$routeBase}{$routePrefix}.store', [], [
            'name' => 'Test {$resourceName}',
        ]);
    });

    test('CREATE: authorized users can create', function () {
        // TODO: Add required fields for {$resourceName}
        \$this->assertAuthorizedUserCanAccess('POST', '{$routeBase}{$routePrefix}.store', [], [
            'name' => 'New {$resourceName}',
        ]);

        \$this->assertDatabaseHas('{$tableName}', [
            'name' => 'New {$resourceName}',
        ]);
    });

    test('CREATE: super admin can create', function () {
        // TODO: Add required fields for {$resourceName}
        \$this->assertSuperAdminCanAccess('POST', '{$routeBase}{$routePrefix}.store', [], [
            'name' => 'Admin {$resourceName}',
        ]);
    });

    test('READ: unauthorized users are denied', function () {
        \$resource = {$resourceName}::factory()->create();
        \$this->assertRequiresPermission('GET', '{$routeBase}{$routePrefix}.show', ['{$routePrefix}' => \$resource->id]);
    });

    test('READ: authorized users can view', function () {
        \$resource = {$resourceName}::factory()->create();
        \$this->assertAuthorizedUserCanAccess('GET', '{$routeBase}{$routePrefix}.show', ['{$routePrefix}' => \$resource->id]);
    });

    test('READ: super admin can view', function () {
        \$resource = {$resourceName}::factory()->create();
        \$this->assertSuperAdminCanAccess('GET', '{$routeBase}{$routePrefix}.show', ['{$routePrefix}' => \$resource->id]);
    });

    test('UPDATE: unauthorized users are denied', function () {
        \$resource = {$resourceName}::factory()->create();
        \$this->assertRequiresPermission('PUT', '{$routeBase}{$routePrefix}.update', ['{$routePrefix}' => \$resource->id], [
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: authorized users can update', function () {
        \$resource = {$resourceName}::factory()->create(['name' => 'Original']);

        \$this->assertAuthorizedUserCanAccess('PUT', '{$routeBase}{$routePrefix}.update', ['{$routePrefix}' => \$resource->id], [
            'name' => 'Updated',
        ]);

        \$this->assertDatabaseHas('{$tableName}', [
            'id' => \$resource->id,
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: super admin can update', function () {
        \$resource = {$resourceName}::factory()->create();
        \$this->assertSuperAdminCanAccess('PUT', '{$routeBase}{$routePrefix}.update', ['{$routePrefix}' => \$resource->id], [
            'name' => 'Admin Updated',
        ]);
    });

    test('DELETE: unauthorized users are denied', function () {
        \$resource = {$resourceName}::factory()->create();
        \$this->assertRequiresPermission('DELETE', '{$routeBase}{$routePrefix}.destroy', ['{$routePrefix}' => \$resource->id]);
    });

    test('DELETE: authorized users can delete', function () {
        \$resource = {$resourceName}::factory()->create();

        \$this->assertAuthorizedUserCanAccess('DELETE', '{$routeBase}{$routePrefix}.destroy', ['{$routePrefix}' => \$resource->id]);

        \$this->assertSoftDeleted('{$tableName}', [
            'id' => \$resource->id,
        ]);
    });

    test('DELETE: super admin can delete', function () {
        \$resource = {$resourceName}::factory()->create();
        \$this->assertSuperAdminCanAccess('DELETE', '{$routeBase}{$routePrefix}.destroy', ['{$routePrefix}' => \$resource->id]);
    });
});
PHP;

// Create the test file
$testFileName = "tests/Feature/Permissions/{$resourceName}PermissionTest.php";
file_put_contents($testFileName, $testContent);

echo "✅ Generated: $testFileName\n";
echo "\n";
echo "Next steps:\n";
echo "1. Review the TODO comments and add required fields\n";
echo "2. Ensure the {$resourceName} model has a factory\n";
echo "3. Run: php artisan test $testFileName\n";
echo "4. Fix the controller by adding \$this->authorize() calls\n";
