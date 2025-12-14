<?php

use Tests\Traits\TestsPermissions;
use Modules\Members\Models\Country;

uses(TestsPermissions::class);

beforeEach(function () {
    $this->setupPermissionUsers([
        'list-country',
        'create-country',
        'read-country',
        'update-country',
        'delete-country',
    ]);
});

describe('Country CRUD Permissions', function () {

    test('LIST: requires authentication', function () {
        $this->assertRequiresAuthentication('GET', 'country.index');
    });

    test('LIST: unauthorized users are denied', function () {
        $this->assertRequiresPermission('GET', 'country.index');
    });

    test('LIST: authorized users can access', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'country.index');
    });

    test('LIST: super admin can access', function () {
        $this->assertSuperAdminCanAccess('GET', 'country.index');
    });

    test('CREATE: unauthorized users are denied', function () {
        // TODO: Add required fields for Country
        $this->assertRequiresPermission('POST', 'country.store', [], [
            'name' => 'Test Country',
        ]);
    });

    test('CREATE: authorized users can create', function () {
        // TODO: Add required fields for Country
        $this->assertAuthorizedUserCanAccess('POST', 'country.store', [], [
            'name' => 'New Country',
        ]);

        $this->assertDatabaseHas('countries', [
            'name' => 'New Country',
        ]);
    });

    test('CREATE: super admin can create', function () {
        // TODO: Add required fields for Country
        $this->assertSuperAdminCanAccess('POST', 'country.store', [], [
            'name' => 'Admin Country',
        ]);
    });

    test('READ: unauthorized users are denied', function () {
        $resource = Country::factory()->create();
        $this->assertRequiresPermission('GET', 'country.show', ['country' => $resource->id]);
    });

    test('READ: authorized users can view', function () {
        $resource = Country::factory()->create();
        $this->assertAuthorizedUserCanAccess('GET', 'country.show', ['country' => $resource->id]);
    });

    test('READ: super admin can view', function () {
        $resource = Country::factory()->create();
        $this->assertSuperAdminCanAccess('GET', 'country.show', ['country' => $resource->id]);
    });

    test('UPDATE: unauthorized users are denied', function () {
        $resource = Country::factory()->create();
        $this->assertRequiresPermission('PUT', 'country.update', ['country' => $resource->id], [
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: authorized users can update', function () {
        $resource = Country::factory()->create(['name' => 'Original']);

        $this->assertAuthorizedUserCanAccess('PUT', 'country.update', ['country' => $resource->id], [
            'name' => 'Updated',
        ]);

        $this->assertDatabaseHas('countries', [
            'id' => $resource->id,
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: super admin can update', function () {
        $resource = Country::factory()->create();
        $this->assertSuperAdminCanAccess('PUT', 'country.update', ['country' => $resource->id], [
            'name' => 'Admin Updated',
        ]);
    });

    test('DELETE: unauthorized users are denied', function () {
        $resource = Country::factory()->create();
        $this->assertRequiresPermission('DELETE', 'country.destroy', ['country' => $resource->id]);
    });

    test('DELETE: authorized users can delete', function () {
        $resource = Country::factory()->create();

        $this->assertAuthorizedUserCanAccess('DELETE', 'country.destroy', ['country' => $resource->id]);

        $this->assertSoftDeleted('countries', [
            'id' => $resource->id,
        ]);
    });

    test('DELETE: super admin can delete', function () {
        $resource = Country::factory()->create();
        $this->assertSuperAdminCanAccess('DELETE', 'country.destroy', ['country' => $resource->id]);
    });
});