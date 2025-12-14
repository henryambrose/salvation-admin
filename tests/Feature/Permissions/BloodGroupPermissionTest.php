<?php

use Tests\Traits\TestsPermissions;
use Modules\Members\Models\BloodGroup;

uses(TestsPermissions::class);

beforeEach(function () {
    $this->setupPermissionUsers([
        'list-blood-group',
        'create-blood-group',
        'read-blood-group',
        'update-blood-group',
        'delete-blood-group',
    ]);
});

describe('BloodGroup CRUD Permissions', function () {

    test('LIST: requires authentication', function () {
        $this->assertRequiresAuthentication('GET', 'blood-group.index');
    });

    test('LIST: unauthorized users are denied', function () {
        $this->assertRequiresPermission('GET', 'blood-group.index');
    });

    test('LIST: authorized users can access', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'blood-group.index');
    });

    test('LIST: super admin can access', function () {
        $this->assertSuperAdminCanAccess('GET', 'blood-group.index');
    });

    test('CREATE: unauthorized users are denied', function () {
        // TODO: Add required fields for BloodGroup
        $this->assertRequiresPermission('POST', 'blood-group.store', [], [
            'name' => 'Test BloodGroup',
        ]);
    });

    test('CREATE: authorized users can create', function () {
        // TODO: Add required fields for BloodGroup
        $this->assertAuthorizedUserCanAccess('POST', 'blood-group.store', [], [
            'name' => 'New BloodGroup',
        ]);

        $this->assertDatabaseHas('blood_groups', [
            'name' => 'New BloodGroup',
        ]);
    });

    test('CREATE: super admin can create', function () {
        // TODO: Add required fields for BloodGroup
        $this->assertSuperAdminCanAccess('POST', 'blood-group.store', [], [
            'name' => 'Admin BloodGroup',
        ]);
    });

    test('READ: unauthorized users are denied', function () {
        $resource = BloodGroup::factory()->create();
        $this->assertRequiresPermission('GET', 'blood-group.show', ['blood_group' => $resource->id]);
    });

    test('READ: authorized users can view', function () {
        $resource = BloodGroup::factory()->create();
        $this->assertAuthorizedUserCanAccess('GET', 'blood-group.show', ['blood_group' => $resource->id]);
    });

    test('READ: super admin can view', function () {
        $resource = BloodGroup::factory()->create();
        $this->assertSuperAdminCanAccess('GET', 'blood-group.show', ['blood_group' => $resource->id]);
    });

    test('UPDATE: unauthorized users are denied', function () {
        $resource = BloodGroup::factory()->create();
        $this->assertRequiresPermission('PUT', 'blood-group.update', ['blood_group' => $resource->id], [
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: authorized users can update', function () {
        $resource = BloodGroup::factory()->create(['name' => 'Original']);

        $this->assertAuthorizedUserCanAccess('PUT', 'blood-group.update', ['blood_group' => $resource->id], [
            'name' => 'Updated',
        ]);

        $this->assertDatabaseHas('blood_groups', [
            'id' => $resource->id,
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: super admin can update', function () {
        $resource = BloodGroup::factory()->create();
        $this->assertSuperAdminCanAccess('PUT', 'blood-group.update', ['blood_group' => $resource->id], [
            'name' => 'Admin Updated',
        ]);
    });

    test('DELETE: unauthorized users are denied', function () {
        $resource = BloodGroup::factory()->create();
        $this->assertRequiresPermission('DELETE', 'blood-group.destroy', ['blood_group' => $resource->id]);
    });

    test('DELETE: authorized users can delete', function () {
        $resource = BloodGroup::factory()->create();

        $this->assertAuthorizedUserCanAccess('DELETE', 'blood-group.destroy', ['blood_group' => $resource->id]);

        $this->assertSoftDeleted('blood_groups', [
            'id' => $resource->id,
        ]);
    });

    test('DELETE: super admin can delete', function () {
        $resource = BloodGroup::factory()->create();
        $this->assertSuperAdminCanAccess('DELETE', 'blood-group.destroy', ['blood_group' => $resource->id]);
    });
});