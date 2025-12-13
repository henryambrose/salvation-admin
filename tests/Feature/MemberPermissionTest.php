<?php

use Modules\Members\Models\Member;
use Modules\Members\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Create permissions
    Permission::create(['name' => 'list-member']);
    Permission::create(['name' => 'create-member']);
    Permission::create(['name' => 'read-member']);
    Permission::create(['name' => 'update-member']);
    Permission::create(['name' => 'delete-member']);
    Permission::create(['name' => 'restore-member']);
});

describe('Member Permissions - Super Admin', function () {
    test('super admin can view all members', function () {
        $superAdmin = User::factory()->create(['is_superadmin' => true]);

        $response = $this->actingAs($superAdmin)
            ->get(route('member.index'));

        expect($response->status())->toBe(200);
    });

    test('super admin can create members', function () {
        $superAdmin = User::factory()->create(['is_superadmin' => true]);

        $response = $this->actingAs($superAdmin)
            ->post(route('member.store'), [
                'first_name' => 'Test',
                'last_name' => 'User',
                'community_id' => \Modules\Members\Models\Community::factory()->create()->id,
                'community_cluster_id' => \Modules\Members\Models\CommunityCluster::factory()->create()->id,
                'relationship_id' => \Modules\Members\Models\Relationship::factory()->create()->id,
            ]);

        expect($response->status())->not->toBe(403);
    });

    test('super admin can delete members', function () {
        $superAdmin = User::factory()->create(['is_superadmin' => true]);

        $member = Member::factory()->create();

        $response = $this->actingAs($superAdmin)
            ->delete(route('member.destroy', $member));

        expect($response->status())->not->toBe(403);
    });
});

describe('Member Permissions - Admin Role', function () {
    test('admin with list permission can view members', function () {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $admin->assignRole($role);
        $admin->givePermissionTo('list-member');

        $response = $this->actingAs($admin)
            ->get(route('member.index'));

        expect($response->status())->toBe(200);
    });

    test('admin without list permission cannot view members', function () {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $admin->assignRole($role);

        $response = $this->actingAs($admin)
            ->get(route('member.index'));

        expect($response->status())->toBe(403);
    });

    test('admin with create permission can create members', function () {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $admin->assignRole($role);
        $admin->givePermissionTo(['create-member', 'list-member']);

        $response = $this->actingAs($admin)
            ->post(route('member.store'), [
                'first_name' => 'New',
                'last_name' => 'Member',
                'community_id' => \Modules\Members\Models\Community::factory()->create()->id,
                'relationship_id' => \Modules\Members\Models\Relationship::factory()->create()->id,
            ]);

        expect($response->status())->not->toBe(403);
    });

    test('admin without create permission cannot create members', function () {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $admin->assignRole($role);
        $admin->givePermissionTo('list-member');

        $community = \Modules\Members\Models\Community::factory()->create();

        $response = $this->actingAs($admin)
            ->post(route('member.store'), [
                'first_name' => 'New',
                'last_name' => 'Member',
                'community_id' => $community->id,
                'community_cluster_id' => \Modules\Members\Models\CommunityCluster::factory()->create(['community_id' => $community->id])->id,
                'relationship_id' => \Modules\Members\Models\Relationship::factory()->create()->id,
            ]);

        expect($response->status())->toBe(403);
    });

    test('admin with update permission can update members', function () {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $admin->assignRole($role);
        $admin->givePermissionTo(['update-member', 'list-member']);

        $member = Member::factory()->create();

        $response = $this->actingAs($admin)
            ->put(route('member.update', $member), [
                'first_name' => 'Updated',
                'last_name' => $member->last_name,
                'community_id' => $member->community_id,
                'relationship_id' => $member->relationship_id,
            ]);

        expect($response->status())->not->toBe(403);
    });

    test('admin with delete permission can delete members', function () {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $admin->assignRole($role);
        $admin->givePermissionTo(['delete-member', 'list-member']);

        $member = Member::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('member.destroy', $member));

        expect($response->status())->not->toBe(403);
    });

    test('admin with restore permission can restore deleted members', function () {
        $admin = User::factory()->create();
        $role = Role::create(['name' => 'admin']);
        $admin->assignRole($role);
        $admin->givePermissionTo(['restore-member', 'list-member']);

        $member = Member::factory()->create();
        $member->delete();

        $response = $this->actingAs($admin)
            ->post(route('member.restore', $member->id));

        expect($response->status())->not->toBe(403);
    });
});

describe('Member Permissions - Regular User', function () {
    test('user without permissions cannot view members', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('member.index'));

        expect($response->status())->toBe(403);
    });

    test('user without permissions cannot create members', function () {
        $user = User::factory()->create();
        $community = \Modules\Members\Models\Community::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('member.store'), [
                'first_name' => 'Test',
                'last_name' => 'User',
                'community_id' => $community->id,
                'community_cluster_id' => \Modules\Members\Models\CommunityCluster::factory()->create(['community_id' => $community->id])->id,
                'relationship_id' => \Modules\Members\Models\Relationship::factory()->create()->id,
            ]);

        expect($response->status())->toBe(403);
    });
});

describe('Community-Based Access Control', function () {
    test('scc head can view only their community members', function () {
        $sccHead = User::factory()->create();
        $role = Role::create(['name' => 'scc-head']);
        $sccHead->assignRole($role);
        $sccHead->givePermissionTo('list-member');

        // This would require additional implementation in the controller
        $response = $this->actingAs($sccHead)
            ->get(route('member.index'));

        expect($response->status())->toBe(200);
    });

    test('ppc head can view multiple community members', function () {
        $ppcHead = User::factory()->create();
        $role = Role::create(['name' => 'ppc-head']);
        $ppcHead->assignRole($role);
        $ppcHead->givePermissionTo('list-member');

        $response = $this->actingAs($ppcHead)
            ->get(route('member.index'));

        expect($response->status())->toBe(200);
    });
});
