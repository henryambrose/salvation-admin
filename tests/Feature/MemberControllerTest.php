<?php

use Modules\Members\Models\Member;
use Modules\Members\Models\Community;
use Modules\Members\Models\CommunityCluster;
use Modules\Members\Models\Relationship;
use Modules\Members\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Create permissions
    Permission::create(['name' => 'list-member']);
    Permission::create(['name' => 'create-member']);
    Permission::create(['name' => 'update-member']);
    Permission::create(['name' => 'delete-member']);
    Permission::create(['name' => 'read-member']);
    Permission::create(['name' => 'restore-member']);

    $this->user = User::factory()->create();
    $this->admin = User::factory()->create();
    $adminRole = Role::create(['name' => 'admin']);
    $this->admin->assignRole($adminRole);
    $this->admin->givePermissionTo(['list-member', 'create-member', 'update-member', 'delete-member', 'read-member', 'restore-member']);
});

test('user can view members index page', function () {
    $this->actingAs($this->admin)
        ->get(route('member.index'))
        ->assertStatus(200);
});

test('user cannot view members without permission', function () {
    $this->actingAs($this->user)
        ->get(route('member.index'))
        ->assertStatus(403);
});

test('admin can create a new member', function () {
    $community = Community::factory()->create();
    $communityCluster = CommunityCluster::factory()->create(['community_id' => $community->id]);
    $relationship = Relationship::factory()->create();

    $memberData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'community_id' => $community->id,
        'community_cluster_id' => $communityCluster->id,
        'relationship_id' => $relationship->id,
        'family_no' => 'SAL-001',
        'date_of_birth' => '1990-01-15',
    ];

    $this->actingAs($this->admin)
        ->post(route('member.store'), $memberData)
        ->assertRedirect();

    $this->assertDatabaseHas('members', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'family_no' => 'SAL-001',
    ]);
});

test('admin can update an existing member', function () {
    $member = Member::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $this->actingAs($this->admin)
        ->put(route('member.update', $member), [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'community_id' => $member->community_id,
            'community_cluster_id' => $member->community_cluster_id,
            'relationship_id' => $member->relationship_id,
            'spouse_source' => 'Member',
            'father_source' => 'Member',
            'mother_source' => 'Member',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('members', [
        'id' => $member->id,
        'first_name' => 'Jane',
        'last_name' => 'Smith',
    ]);
});

test('admin can soft delete a member', function () {
    $member = Member::factory()->create();

    $this->actingAs($this->admin)
        ->delete(route('member.destroy', $member))
        ->assertRedirect();

    $this->assertSoftDeleted('members', ['id' => $member->id]);
});

test('admin can restore a soft deleted member', function () {
    $member = Member::factory()->create();
    $member->delete();

    $this->actingAs($this->admin)
        ->post(route('member.restore', $member->id))
        ->assertRedirect();

    $this->assertDatabaseHas('members', [
        'id' => $member->id,
        'deleted_at' => null,
    ]);
});

test('member index can be filtered by community', function () {
    $community = Community::factory()->create();
    Member::factory()->count(5)->create(['community_id' => $community->id]);
    Member::factory()->count(3)->create(); // Different community

    $response = $this->actingAs($this->admin)
        ->get(route('member.index', ['community_id' => $community->id]))
        ->assertStatus(200);
});

test('member index can be searched by name', function () {
    Member::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
    Member::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith']);

    $this->actingAs($this->admin)
        ->get(route('member.index', ['search' => 'John']))
        ->assertStatus(200);
});

test('member requires first name', function () {
    $this->actingAs($this->admin)
        ->post(route('member.store'), [
            'last_name' => 'Doe',
            'community_id' => Community::factory()->create()->id,
            'relationship_id' => Relationship::factory()->create()->id,
        ])
        ->assertSessionHasErrors(['first_name']);
});

test('member requires community', function () {
    $this->actingAs($this->admin)
        ->post(route('member.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'relationship_id' => Relationship::factory()->create()->id,
        ])
        ->assertSessionHasErrors(['community_id']);
});

test('member requires relationship', function () {
    $this->actingAs($this->admin)
        ->post(route('member.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'community_id' => Community::factory()->create()->id,
        ])
        ->assertSessionHasErrors(['relationship_id']);
});

test('member date of birth cannot be in future', function () {
    $futureDate = now()->addDays(1)->format('Y-m-d');

    $this->actingAs($this->admin)
        ->post(route('member.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'date_of_birth' => $futureDate,
            'community_id' => Community::factory()->create()->id,
            'relationship_id' => Relationship::factory()->create()->id,
        ])
        ->assertSessionHasErrors(['date_of_birth']);
});

test('member email must be valid format', function () {
    $this->actingAs($this->admin)
        ->post(route('member.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-email',
            'community_id' => Community::factory()->create()->id,
            'relationship_id' => Relationship::factory()->create()->id,
        ])
        ->assertSessionHasErrors(['email']);
});

test('member phone number must be valid indian format', function () {
    $this->actingAs($this->admin)
        ->post(route('member.store'), [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'contact_no_1' => '123', // Invalid
            'community_id' => Community::factory()->create()->id,
            'relationship_id' => Relationship::factory()->create()->id,
        ])
        ->assertSessionHasErrors(['contact_no_1']);
});
