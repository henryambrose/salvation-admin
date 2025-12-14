<?php

use Tests\Traits\TestsPermissions;
use Modules\Members\Models\State;

uses(TestsPermissions::class);

beforeEach(function () {
    $this->setupPermissionUsers([
        'list-state',
        'create-state',
        'read-state',
        'update-state',
        'delete-state',
    ]);
});

describe('State CRUD Permissions', function () {

    test('LIST: requires authentication', function () {
        $this->assertRequiresAuthentication('GET', 'state.index');
    });

    test('LIST: unauthorized users are denied', function () {
        $this->assertRequiresPermission('GET', 'state.index');
    });

    test('LIST: authorized users can access', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'state.index');
    });

    test('LIST: super admin can access', function () {
        $this->assertSuperAdminCanAccess('GET', 'state.index');
    });

    test('CREATE: unauthorized users are denied', function () {
        $country = \Modules\Members\Models\Country::factory()->create();
        $this->assertRequiresPermission('POST', 'state.store', [], [
            'name' => 'Test State',
            'country_id' => $country->id,
        ]);
    });

    test('CREATE: authorized users can create', function () {
        $country = \Modules\Members\Models\Country::factory()->create();
        $this->assertAuthorizedUserCanAccess('POST', 'state.store', [], [
            'name' => 'New State',
            'country_id' => $country->id,
        ]);

        $this->assertDatabaseHas('states', [
            'name' => 'New State',
        ]);
    });

    test('CREATE: super admin can create', function () {
        $country = \Modules\Members\Models\Country::factory()->create();
        $this->assertSuperAdminCanAccess('POST', 'state.store', [], [
            'name' => 'Admin State',
            'country_id' => $country->id,
        ]);
    });

    test('READ: unauthorized users are denied', function () {
        $resource = State::factory()->create();
        $this->assertRequiresPermission('GET', 'state.show', ['state' => $resource->id]);
    });

    test('READ: authorized users can view', function () {
        $resource = State::factory()->create();
        $this->assertAuthorizedUserCanAccess('GET', 'state.show', ['state' => $resource->id]);
    });

    test('READ: super admin can view', function () {
        $resource = State::factory()->create();
        $this->assertSuperAdminCanAccess('GET', 'state.show', ['state' => $resource->id]);
    });

    test('UPDATE: unauthorized users are denied', function () {
        $resource = State::factory()->create();
        $this->assertRequiresPermission('PUT', 'state.update', ['state' => $resource->id], [
            'name' => 'Updated',
            'country_id' => $resource->country_id,
        ]);
    });

    test('UPDATE: authorized users can update', function () {
        $resource = State::factory()->create(['name' => 'Original']);

        $this->assertAuthorizedUserCanAccess('PUT', 'state.update', ['state' => $resource->id], [
            'name' => 'Updated',
            'country_id' => $resource->country_id,
        ]);

        $this->assertDatabaseHas('states', [
            'id' => $resource->id,
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: super admin can update', function () {
        $resource = State::factory()->create();
        $this->assertSuperAdminCanAccess('PUT', 'state.update', ['state' => $resource->id], [
            'name' => 'Admin Updated',
            'country_id' => $resource->country_id,
        ]);
    });

    test('DELETE: unauthorized users are denied', function () {
        $resource = State::factory()->create();
        $this->assertRequiresPermission('DELETE', 'state.destroy', ['state' => $resource->id]);
    });

    test('DELETE: authorized users can delete', function () {
        $resource = State::factory()->create();

        $this->assertAuthorizedUserCanAccess('DELETE', 'state.destroy', ['state' => $resource->id]);

        $this->assertSoftDeleted('states', [
            'id' => $resource->id,
        ]);
    });

    test('DELETE: super admin can delete', function () {
        $resource = State::factory()->create();
        $this->assertSuperAdminCanAccess('DELETE', 'state.destroy', ['state' => $resource->id]);
    });
});