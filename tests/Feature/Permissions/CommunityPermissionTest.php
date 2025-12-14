<?php

use Tests\Traits\TestsPermissions;
use Modules\Members\Models\Community;
use Modules\Members\Models\Zone;

uses(TestsPermissions::class);

beforeEach(function () {
    $this->setupPermissionUsers([
        'list-community',
        'create-community',
        'read-community',
        'update-community',
        'delete-community',
    ]);
});

describe('Community CRUD Permissions', function () {

    describe('List/Index Permission', function () {
        test('unauthenticated users cannot list communities', function () {
            $this->assertRequiresAuthentication('GET', 'community.index');
        });

        test('users without list-community permission are denied', function () {
            $this->assertRequiresPermission('GET', 'community.index');
        });

        test('users with list-community permission can list communities', function () {
            $this->assertAuthorizedUserCanAccess('GET', 'community.index');
        });

        test('super admin can list communities', function () {
            $this->assertSuperAdminCanAccess('GET', 'community.index');
        });
    });

    describe('Create Permission', function () {
        test('users without create-community permission cannot create', function () {
            $zone = Zone::factory()->create();

            $this->assertRequiresPermission('POST', 'community.store', [], [
                'name' => 'Test Community',
                'zone_id' => $zone->id,
            ]);
        });

        test('users with create-community permission can create', function () {
            $zone = Zone::factory()->create();

            $this->assertAuthorizedUserCanAccess('POST', 'community.store', [], [
                'name' => 'Test Community',
                'zone_id' => $zone->id,
            ]);

            $this->assertDatabaseHas('communities', [
                'name' => 'Test Community',
                'zone_id' => $zone->id,
            ]);
        });

        test('super admin can create communities', function () {
            $zone = Zone::factory()->create();

            $this->assertSuperAdminCanAccess('POST', 'community.store', [], [
                'name' => 'Super Admin Community',
                'zone_id' => $zone->id,
            ]);
        });
    });

    describe('Read/Show Permission', function () {
        test('users without read-community permission cannot view details', function () {
            $community = Community::factory()->create();

            $this->assertRequiresPermission('GET', 'community.show', ['community' => $community->id]);
        });

        test('users with read-community permission can view details', function () {
            $community = Community::factory()->create();

            $this->assertAuthorizedUserCanAccess('GET', 'community.show', ['community' => $community->id]);
        });

        test('super admin can view community details', function () {
            $community = Community::factory()->create();

            $this->assertSuperAdminCanAccess('GET', 'community.show', ['community' => $community->id]);
        });
    });

    describe('Update Permission', function () {
        test('users without update-community permission cannot update', function () {
            $community = Community::factory()->create();

            $this->assertRequiresPermission('PUT', 'community.update', ['community' => $community->id], [
                'name' => 'Updated Name',
                'zone_id' => $community->zone_id,
            ]);
        });

        test('users with update-community permission can update', function () {
            $community = Community::factory()->create(['name' => 'Original Name']);

            $this->assertAuthorizedUserCanAccess('PUT', 'community.update', ['community' => $community->id], [
                'name' => 'Updated Name',
                'zone_id' => $community->zone_id,
            ]);

            $this->assertDatabaseHas('communities', [
                'id' => $community->id,
                'name' => 'Updated Name',
            ]);
        });

        test('super admin can update communities', function () {
            $community = Community::factory()->create();

            $this->assertSuperAdminCanAccess('PUT', 'community.update', ['community' => $community->id], [
                'name' => 'Admin Updated',
                'zone_id' => $community->zone_id,
            ]);
        });
    });

    describe('Delete Permission', function () {
        test('users without delete-community permission cannot delete', function () {
            $community = Community::factory()->create();

            $this->assertRequiresPermission('DELETE', 'community.destroy', ['community' => $community->id]);
        });

        test('users with delete-community permission can delete', function () {
            $community = Community::factory()->create();

            $this->assertAuthorizedUserCanAccess('DELETE', 'community.destroy', ['community' => $community->id]);

            $this->assertSoftDeleted('communities', [
                'id' => $community->id,
            ]);
        });

        test('super admin can delete communities', function () {
            $community = Community::factory()->create();

            $this->assertSuperAdminCanAccess('DELETE', 'community.destroy', ['community' => $community->id]);
        });
    });
});

describe('Community Permission Edge Cases', function () {
    test('users with only read permission cannot create', function () {
        $user = \Modules\Members\Models\User::factory()->create();
        $user->givePermissionTo('read-community');

        $zone = Zone::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('community.store'), [
                'name' => 'Test',
                'zone_id' => $zone->id,
            ]);

        expect($response->status())->toBe(403);
    });

    test('users with only create permission cannot update', function () {
        $user = \Modules\Members\Models\User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'create-community']);
        $user->givePermissionTo('create-community');

        $community = Community::factory()->create();

        $response = $this->actingAs($user)
            ->put(route('community.update', $community), [
                'name' => 'Updated',
                'zone_id' => $community->zone_id,
            ]);

        expect($response->status())->toBe(403);
    });

    test('users with only list permission cannot delete', function () {
        $user = \Modules\Members\Models\User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'list-community']);
        $user->givePermissionTo('list-community');

        $community = Community::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('community.destroy', $community));

        expect($response->status())->toBe(403);
    });
});
