<?php

use Tests\Traits\TestsPermissions;
use Modules\Members\Models\Zone;

uses(TestsPermissions::class);

beforeEach(function () {
    $this->setupPermissionUsers([
        'list-zone',
        'create-zone',
        'read-zone',
        'update-zone',
        'delete-zone',
    ]);
});

describe('Zone CRUD Permissions', function () {

    test('LIST: unauthorized users are denied', function () {
        $this->assertRequiresPermission('GET', 'zone.index');
    });

    test('LIST: authorized users can access', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'zone.index');
    });

    test('LIST: super admin can access', function () {
        $this->assertSuperAdminCanAccess('GET', 'zone.index');
    });

    test('CREATE: unauthorized users are denied', function () {
        $this->assertRequiresPermission('POST', 'zone.store', [], [
            'name' => 'Test Zone',
            'description' => 'Test Description',
        ]);
    });

    test('CREATE: authorized users can create', function () {
        $this->assertAuthorizedUserCanAccess('POST', 'zone.store', [], [
            'name' => 'New Zone',
            'description' => 'Description',
        ]);

        $this->assertDatabaseHas('zones', ['name' => 'New Zone']);
    });

    test('CREATE: super admin can create', function () {
        $this->assertSuperAdminCanAccess('POST', 'zone.store', [], [
            'name' => 'Admin Zone',
        ]);
    });

    test('READ: unauthorized users are denied', function () {
        $zone = Zone::factory()->create();
        $this->assertRequiresPermission('GET', 'zone.show', ['zone' => $zone->id]);
    });

    test('READ: authorized users can view', function () {
        $zone = Zone::factory()->create();
        $this->assertAuthorizedUserCanAccess('GET', 'zone.show', ['zone' => $zone->id]);
    });

    test('UPDATE: unauthorized users are denied', function () {
        $zone = Zone::factory()->create();
        $this->assertRequiresPermission('PUT', 'zone.update', ['zone' => $zone->id], [
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: authorized users can update', function () {
        $zone = Zone::factory()->create(['name' => 'Original']);

        $this->assertAuthorizedUserCanAccess('PUT', 'zone.update', ['zone' => $zone->id], [
            'name' => 'Updated',
        ]);

        $this->assertDatabaseHas('zones', [
            'id' => $zone->id,
            'name' => 'Updated',
        ]);
    });

    test('DELETE: unauthorized users are denied', function () {
        $zone = Zone::factory()->create();
        $this->assertRequiresPermission('DELETE', 'zone.destroy', ['zone' => $zone->id]);
    });

    test('DELETE: authorized users can delete', function () {
        $zone = Zone::factory()->create();

        $this->assertAuthorizedUserCanAccess('DELETE', 'zone.destroy', ['zone' => $zone->id]);

        $this->assertSoftDeleted('zones', ['id' => $zone->id]);
    });
});
