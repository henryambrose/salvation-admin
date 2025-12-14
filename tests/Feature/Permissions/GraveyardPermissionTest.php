<?php

use Tests\Traits\TestsPermissions;

uses(TestsPermissions::class);

describe('Grave CRUD Permissions', function () {

    beforeEach(function () {
        $this->setupPermissionUsers([
            'list-grave',
            'create-grave',
            'read-grave',
            'update-grave',
            'delete-grave',
        ]);
    });

    test('LIST: requires authentication', function () {
        $this->assertRequiresAuthentication('GET', 'graveyard.graves.index');
    });

    test('LIST: requires list-grave permission', function () {
        $this->assertRequiresPermission('GET', 'graveyard.graves.index');
    });

    test('LIST: authorized users can list', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'graveyard.graves.index');
    });

    test('LIST: super admin can list', function () {
        $this->assertSuperAdminCanAccess('GET', 'graveyard.graves.index');
    });

    test('CREATE: requires create-grave permission', function () {
        $this->assertRequiresPermission('POST', 'graveyard.graves.store', [], []);
    });

    test('CREATE: authorized users can create', function () {
        $this->assertAuthorizedUserCanAccess('POST', 'graveyard.graves.store', [], []);
    });

    test('UPDATE: requires update-grave permission', function () {
        $this->assertRequiresPermission('PUT', 'graveyard.graves.update', ['grave' => 1], []);
    });

    test('DELETE: requires delete-grave permission', function () {
        $this->assertRequiresPermission('DELETE', 'graveyard.graves.destroy', ['grave' => 1]);
    });
});

describe('Permanent Grave Booking Permissions', function () {

    beforeEach(function () {
        $this->setupPermissionUsers([
            'list-permanent-grave-booking',
            'create-permanent-grave-booking',
            'update-permanent-grave-booking',
            'delete-permanent-grave-booking',
        ]);
    });

    test('LIST: requires list permission', function () {
        $this->assertRequiresPermission('GET', 'graveyard.permanent-grave-bookings.index');
    });

    test('LIST: authorized users can list', function () {
        $this->assertAuthorizedUserCanAccess('GET', 'graveyard.permanent-grave-bookings.index');
    });

    test('CREATE: requires create permission', function () {
        $this->assertRequiresPermission('POST', 'graveyard.permanent-grave-bookings.store', [], []);
    });

    test('CREATE: authorized users can create', function () {
        $this->assertAuthorizedUserCanAccess('POST', 'graveyard.permanent-grave-bookings.store', [], []);
    });
});

describe('Niche Transfer Permissions', function () {

    beforeEach(function () {
        $this->setupPermissionUsers([
            'list-niche-transfer',
            'create-niche-transfer',
            'approve-niche-transfer',
            'reject-niche-transfer',
        ]);
    });

    test('LIST: requires permission', function () {
        $this->assertRequiresPermission('GET', 'graveyard.niche-transfers.index');
    });

    test('CREATE: requires permission', function () {
        $this->assertRequiresPermission('POST', 'graveyard.niche-transfers.store', [], []);
    });
});
