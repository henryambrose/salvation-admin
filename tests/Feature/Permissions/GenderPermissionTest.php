<?php

use Tests\Traits\TestsPermissions;
use Modules\Members\Models\Gender;

uses(TestsPermissions::class);

beforeEach(function () {
    $this->setupPermissionUsers([
        'list-gender',
        'create-gender',
        'read-gender',
        'update-gender',
        'delete-gender',
    ]);
});

describe('Gender CRUD Permissions', function () {

    test('LIST: requires authentication', function () {
        $this->assertRequiresAuthentication('GET', 'gender.index');
    });

    test('LIST: unauthorized users are denied', function () {
        $this->assertRequiresPermission('GET', 'gender.index');
    });

    test('LIST: authorized users can access', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'gender.index');
    });

    test('LIST: super admin can access', function () {
        $this->assertSuperAdminCanAccess('GET', 'gender.index');
    });

    test('CREATE: unauthorized users are denied', function () {
        // TODO: Add required fields for Gender
        $this->assertRequiresPermission('POST', 'gender.store', [], [
            'name' => 'Test Gender',
        ]);
    });

    test('CREATE: authorized users can create', function () {
        // TODO: Add required fields for Gender
        $this->assertAuthorizedUserCanAccess('POST', 'gender.store', [], [
            'name' => 'New Gender',
        ]);

        $this->assertDatabaseHas('genders', [
            'name' => 'New Gender',
        ]);
    });

    test('CREATE: super admin can create', function () {
        // TODO: Add required fields for Gender
        $this->assertSuperAdminCanAccess('POST', 'gender.store', [], [
            'name' => 'Admin Gender',
        ]);
    });

    test('READ: unauthorized users are denied', function () {
        $resource = Gender::factory()->create();
        $this->assertRequiresPermission('GET', 'gender.show', ['gender' => $resource->id]);
    });

    test('READ: authorized users can view', function () {
        $resource = Gender::factory()->create();
        $this->assertAuthorizedUserCanAccess('GET', 'gender.show', ['gender' => $resource->id]);
    });

    test('READ: super admin can view', function () {
        $resource = Gender::factory()->create();
        $this->assertSuperAdminCanAccess('GET', 'gender.show', ['gender' => $resource->id]);
    });

    test('UPDATE: unauthorized users are denied', function () {
        $resource = Gender::factory()->create();
        $this->assertRequiresPermission('PUT', 'gender.update', ['gender' => $resource->id], [
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: authorized users can update', function () {
        $resource = Gender::factory()->create(['name' => 'Original']);

        $this->assertAuthorizedUserCanAccess('PUT', 'gender.update', ['gender' => $resource->id], [
            'name' => 'Updated',
        ]);

        $this->assertDatabaseHas('genders', [
            'id' => $resource->id,
            'name' => 'Updated',
        ]);
    });

    test('UPDATE: super admin can update', function () {
        $resource = Gender::factory()->create();
        $this->assertSuperAdminCanAccess('PUT', 'gender.update', ['gender' => $resource->id], [
            'name' => 'Admin Updated',
        ]);
    });

    test('DELETE: unauthorized users are denied', function () {
        $resource = Gender::factory()->create();
        $this->assertRequiresPermission('DELETE', 'gender.destroy', ['gender' => $resource->id]);
    });

    test('DELETE: authorized users can delete', function () {
        $resource = Gender::factory()->create();

        $this->assertAuthorizedUserCanAccess('DELETE', 'gender.destroy', ['gender' => $resource->id]);

        $this->assertSoftDeleted('genders', [
            'id' => $resource->id,
        ]);
    });

    test('DELETE: super admin can delete', function () {
        $resource = Gender::factory()->create();
        $this->assertSuperAdminCanAccess('DELETE', 'gender.destroy', ['gender' => $resource->id]);
    });
});