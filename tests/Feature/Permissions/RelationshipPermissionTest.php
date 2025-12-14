<?php

use Tests\Traits\TestsPermissions;
use Modules\Members\Models\Relationship;

uses(TestsPermissions::class);

beforeEach(function () {
    $this->setupPermissionUsers([
        'list-relationship',
        'create-relationship',
        'read-relationship',
        'update-relationship',
        'delete-relationship',
    ]);
});

describe('Relationship CRUD Permissions', function () {

    test('LIST: requires authentication', function () {
        $this->assertRequiresAuthentication('GET', 'relationship.index');
    });

    test('LIST: unauthorized users are denied', function () {
        $this->assertRequiresPermission('GET', 'relationship.index');
    });

    test('LIST: authorized users can access', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'relationship.index');
    });

    test('LIST: super admin can access', function () {
        $this->assertSuperAdminCanAccess('GET', 'relationship.index');
    });

    test('CREATE: unauthorized users are denied', function () {
        // TODO: Add required fields for Relationship
        $this->assertRequiresPermission('POST', 'relationship.store', [], [
            'name' => 'Test Relationship',
        ]);
    });

    test('CREATE: authorized users can create', function () {
        // TODO: Add required fields for Relationship
        $this->assertAuthorizedUserCanAccess('POST', 'relationship.store', [], [
            'name' => 'New Relationship',
        ]);

        $this->assertDatabaseHas('relationships', [
            'name' => 'New Relationship',
        ]);
    });

    test('CREATE: super admin can create', function () {
        // TODO: Add required fields for Relationship
        $this->assertSuperAdminCanAccess('POST', 'relationship.store', [], [
            'name' => 'Admin Relationship',
        ]);
    });

    test('READ: unauthorized users are denied', function () {
        $resource = Relationship::factory()->create();
        $this->assertRequiresPermission('GET', 'relationship.show', ['relationship' => $resource->id]);
    });

    test('READ: authorized users can view', function () {
        $resource = Relationship::factory()->create();
        $this->assertAuthorizedUserCanAccess('GET', 'relationship.show', ['relationship' => $resource->id]);
    });

    test('READ: super admin can view', function () {
        $resource = Relationship::factory()->create();
        $this->assertSuperAdminCanAccess('GET', 'relationship.show', ['relationship' => $resource->id]);
    });

    test('UPDATE: unauthorized users are denied', function () {
        $resource = Relationship::factory()->create();
        $this->assertRequiresPermission('PUT', 'relationship.update', ['relationship' => $resource->id], [
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: authorized users can update', function () {
        $resource = Relationship::factory()->create(['name' => 'Original']);

        $this->assertAuthorizedUserCanAccess('PUT', 'relationship.update', ['relationship' => $resource->id], [
            'name' => 'Updated',
        ]);

        $this->assertDatabaseHas('relationships', [
            'id' => $resource->id,
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: super admin can update', function () {
        $resource = Relationship::factory()->create();
        $this->assertSuperAdminCanAccess('PUT', 'relationship.update', ['relationship' => $resource->id], [
            'name' => 'Admin Updated',
        ]);
    });

    test('DELETE: unauthorized users are denied', function () {
        $resource = Relationship::factory()->create();
        $this->assertRequiresPermission('DELETE', 'relationship.destroy', ['relationship' => $resource->id]);
    });

    test('DELETE: authorized users can delete', function () {
        $resource = Relationship::factory()->create();

        $this->assertAuthorizedUserCanAccess('DELETE', 'relationship.destroy', ['relationship' => $resource->id]);

        $this->assertSoftDeleted('relationships', [
            'id' => $resource->id,
        ]);
    });

    test('DELETE: super admin can delete', function () {
        $resource = Relationship::factory()->create();
        $this->assertSuperAdminCanAccess('DELETE', 'relationship.destroy', ['relationship' => $resource->id]);
    });
});