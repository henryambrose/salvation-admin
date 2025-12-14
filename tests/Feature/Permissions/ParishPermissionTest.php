<?php

use Tests\Traits\TestsPermissions;
use Modules\Members\Models\Parish;

uses(TestsPermissions::class);

beforeEach(function () {
    $this->setupPermissionUsers([
        'list-parish',
        'create-parish',
        'read-parish',
        'update-parish',
        'delete-parish',
    ]);
});

describe('Parish CRUD Permissions', function () {

    test('LIST: requires authentication', function () {
        $this->assertRequiresAuthentication('GET', 'parish.index');
    });

    test('LIST: unauthorized users are denied', function () {
        $this->assertRequiresPermission('GET', 'parish.index');
    });

    test('LIST: authorized users can access', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'parish.index');
    });

    test('LIST: super admin can access', function () {
        $this->assertSuperAdminCanAccess('GET', 'parish.index');
    });

    test('CREATE: unauthorized users are denied', function () {
        $this->assertRequiresPermission('POST', 'parish.store', [], [
            'deanery' => 'Test Deanery',
            'name' => 'Test Parish',
        ]);
    });

    test('CREATE: authorized users can create', function () {
        $this->assertAuthorizedUserCanAccess('POST', 'parish.store', [], [
            'deanery' => 'Test Deanery',
            'name' => 'New Parish',
        ]);

        $this->assertDatabaseHas('parishes', [
            'name' => 'New Parish',
        ]);
    });

    test('CREATE: super admin can create', function () {
        $this->assertSuperAdminCanAccess('POST', 'parish.store', [], [
            'deanery' => 'Admin Deanery',
            'name' => 'Admin Parish',
        ]);
    });

    test('READ: unauthorized users are denied', function () {
        $resource = Parish::factory()->create();
        $this->assertRequiresPermission('GET', 'parish.show', ['parish' => $resource->id]);
    });

    test('READ: authorized users can view', function () {
        $resource = Parish::factory()->create();
        $this->assertAuthorizedUserCanAccess('GET', 'parish.show', ['parish' => $resource->id]);
    });

    test('READ: super admin can view', function () {
        $resource = Parish::factory()->create();
        $this->assertSuperAdminCanAccess('GET', 'parish.show', ['parish' => $resource->id]);
    });

    test('UPDATE: unauthorized users are denied', function () {
        $resource = Parish::factory()->create();
        $this->assertRequiresPermission('PUT', 'parish.update', ['parish' => $resource->id], [
            'deanery' => $resource->deanery,
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: authorized users can update', function () {
        $resource = Parish::factory()->create(['name' => 'Original']);

        $this->assertAuthorizedUserCanAccess('PUT', 'parish.update', ['parish' => $resource->id], [
            'deanery' => $resource->deanery,
            'name' => 'Updated',
        ]);

        $this->assertDatabaseHas('parishes', [
            'id' => $resource->id,
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: super admin can update', function () {
        $resource = Parish::factory()->create();
        $this->assertSuperAdminCanAccess('PUT', 'parish.update', ['parish' => $resource->id], [
            'deanery' => $resource->deanery,
            'name' => 'Admin Updated',
        ]);
    });

    test('DELETE: unauthorized users are denied', function () {
        $resource = Parish::factory()->create();
        $this->assertRequiresPermission('DELETE', 'parish.destroy', ['parish' => $resource->id]);
    });

    test('DELETE: authorized users can delete', function () {
        $resource = Parish::factory()->create();

        $this->assertAuthorizedUserCanAccess('DELETE', 'parish.destroy', ['parish' => $resource->id]);

        $this->assertSoftDeleted('parishes', [
            'id' => $resource->id,
        ]);
    });

    test('DELETE: super admin can delete', function () {
        $resource = Parish::factory()->create();
        $this->assertSuperAdminCanAccess('DELETE', 'parish.destroy', ['parish' => $resource->id]);
    });
});