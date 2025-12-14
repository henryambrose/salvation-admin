<?php

use Tests\Traits\TestsPermissions;
use Modules\Members\Models\City;

uses(TestsPermissions::class);

beforeEach(function () {
    $this->setupPermissionUsers([
        'list-city',
        'create-city',
        'read-city',
        'update-city',
        'delete-city',
    ]);
});

describe('City CRUD Permissions', function () {

    test('LIST: requires authentication', function () {
        $this->assertRequiresAuthentication('GET', 'city.index');
    });

    test('LIST: unauthorized users are denied', function () {
        $this->assertRequiresPermission('GET', 'city.index');
    });

    test('LIST: authorized users can access', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'city.index');
    });

    test('LIST: super admin can access', function () {
        $this->assertSuperAdminCanAccess('GET', 'city.index');
    });

    test('CREATE: unauthorized users are denied', function () {
        $state = \Modules\Members\Models\State::factory()->create();
        $this->assertRequiresPermission('POST', 'city.store', [], [
            'name' => 'Test City',
            'state_id' => $state->id,
        ]);
    });

    test('CREATE: authorized users can create', function () {
        $state = \Modules\Members\Models\State::factory()->create();
        $this->assertAuthorizedUserCanAccess('POST', 'city.store', [], [
            'name' => 'New City',
            'state_id' => $state->id,
        ]);

        $this->assertDatabaseHas('cities', [
            'name' => 'New City',
        ]);
    });

    test('CREATE: super admin can create', function () {
        $state = \Modules\Members\Models\State::factory()->create();
        $this->assertSuperAdminCanAccess('POST', 'city.store', [], [
            'name' => 'Admin City',
            'state_id' => $state->id,
        ]);
    });

    test('READ: unauthorized users are denied', function () {
        $resource = City::factory()->create();
        $this->assertRequiresPermission('GET', 'city.show', ['city' => $resource->id]);
    });

    test('READ: authorized users can view', function () {
        $resource = City::factory()->create();
        $this->assertAuthorizedUserCanAccess('GET', 'city.show', ['city' => $resource->id]);
    });

    test('READ: super admin can view', function () {
        $resource = City::factory()->create();
        $this->assertSuperAdminCanAccess('GET', 'city.show', ['city' => $resource->id]);
    });

    test('UPDATE: unauthorized users are denied', function () {
        $resource = City::factory()->create();
        $this->assertRequiresPermission('PUT', 'city.update', ['city' => $resource->id], [
            'name' => 'Updated',
            'state_id' => $resource->state_id,
        ]);
    });

    test('UPDATE: authorized users can update', function () {
        $resource = City::factory()->create(['name' => 'Original']);

        $this->assertAuthorizedUserCanAccess('PUT', 'city.update', ['city' => $resource->id], [
            'name' => 'Updated',
            'state_id' => $resource->state_id,
        ]);

        $this->assertDatabaseHas('cities', [
            'id' => $resource->id,
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: super admin can update', function () {
        $resource = City::factory()->create();
        $this->assertSuperAdminCanAccess('PUT', 'city.update', ['city' => $resource->id], [
            'name' => 'Admin Updated',
            'state_id' => $resource->state_id,
        ]);
    });

    test('DELETE: unauthorized users are denied', function () {
        $resource = City::factory()->create();
        $this->assertRequiresPermission('DELETE', 'city.destroy', ['city' => $resource->id]);
    });

    test('DELETE: authorized users can delete', function () {
        $resource = City::factory()->create();

        $this->assertAuthorizedUserCanAccess('DELETE', 'city.destroy', ['city' => $resource->id]);

        $this->assertSoftDeleted('cities', [
            'id' => $resource->id,
        ]);
    });

    test('DELETE: super admin can delete', function () {
        $resource = City::factory()->create();
        $this->assertSuperAdminCanAccess('DELETE', 'city.destroy', ['city' => $resource->id]);
    });
});