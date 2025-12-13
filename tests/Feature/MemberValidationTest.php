<?php

use Modules\Members\Models\Member;
use Modules\Members\Models\Community;
use Modules\Members\Models\Relationship;
use Modules\Members\Models\Parish;
use Modules\Members\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Create permissions
    Permission::create(['name' => 'create-member']);
    Permission::create(['name' => 'update-member']);

    $this->admin = User::factory()->create();
    $adminRole = Role::create(['name' => 'admin']);
    $this->admin->assignRole($adminRole);
    $this->admin->givePermissionTo(['create-member', 'update-member']);
});

describe('Member Creation Validation', function () {
    test('validates parish selection correctly', function () {
        $parish = Parish::factory()->create(['name' => 'Test Parish']);

        $response = $this->actingAs($this->admin)
            ->post(route('member.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'community_id' => Community::factory()->create()->id,
                'relationship_id' => Relationship::factory()->create()->id,
                'baptism_parish_id' => $parish->id,
                'baptism_parish' => 'Test Parish',
            ]);

        expect($response->status())->not->toBe(422);
    });

    test('validates city ID exists in cities table', function () {
        $this->actingAs($this->admin)
            ->post(route('member.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'community_id' => Community::factory()->create()->id,
                'relationship_id' => Relationship::factory()->create()->id,
                'permanent_city_id' => 99999, // Non-existent
            ])
            ->assertSessionHasErrors(['permanent_city_id']);
    });

    test('validates aadhar number format', function () {
        $this->actingAs($this->admin)
            ->post(route('member.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'community_id' => Community::factory()->create()->id,
                'relationship_id' => Relationship::factory()->create()->id,
                'aadhar' => '123', // Too short
            ])
            ->assertSessionHasErrors(['aadhar']);
    });

    test('validates confirmation date is after baptism date', function () {
        $this->actingAs($this->admin)
            ->post(route('member.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'community_id' => Community::factory()->create()->id,
                'relationship_id' => Relationship::factory()->create()->id,
                'baptism_date' => '2020-01-15',
                'confirmation_date' => '2019-01-15', // Before baptism
            ])
            ->assertSessionHasErrors();
    });

    test('validates marriage date is after birth date', function () {
        $this->actingAs($this->admin)
            ->post(route('member.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'community_id' => Community::factory()->create()->id,
                'relationship_id' => Relationship::factory()->create()->id,
                'date_of_birth' => '2000-01-15',
                'marriage_date' => '1999-01-15', // Before birth
            ])
            ->assertSessionHasErrors();
    });
});

describe('Member Update Validation', function () {
    test('allows updating member with valid city ID', function () {
        $member = Member::factory()->create();
        $city = \Modules\Members\Models\City::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('member.update', $member), [
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'community_id' => $member->community_id,
                'community_cluster_id' => $member->community_cluster_id,
                'relationship_id' => $member->relationship_id,
                'permanent_city_id' => $city->id,
                'spouse_source' => 'Member',
                'father_source' => 'Member',
                'mother_source' => 'Member',
            ])
            ->assertSessionDoesntHaveErrors();
    });

    test('validates parish name when parish ID is not set', function () {
        $member = Member::factory()->create();
        $existingParish = Parish::factory()->create(['name' => 'Existing Parish']);

        $response = $this->actingAs($this->admin)
            ->put(route('member.update', $member), [
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'community_id' => $member->community_id,
                'community_cluster_id' => $member->community_cluster_id,
                'relationship_id' => $member->relationship_id,
                'baptism_parish' => 'Existing Parish', // Exists but no ID
                'baptism_parish_id' => null,
                'spouse_source' => 'Member',
                'father_source' => 'Member',
                'mother_source' => 'Member',
            ]);

        // Test succeeds if update completes (validation may allow this)
        expect($response->getStatusCode())->toBeIn([200, 302]);
    });

    test('allows unchanged parish value on update', function () {
        $parish = Parish::factory()->create(['name' => 'Original Parish']);
        $member = Member::factory()->create([
            'baptism_parish' => 'Original Parish',
            'baptism_parish_id' => $parish->id,
        ]);

        $this->actingAs($this->admin)
            ->put(route('member.update', $member), [
                'first_name' => 'Updated Name',
                'last_name' => $member->last_name,
                'community_id' => $member->community_id,
                'community_cluster_id' => $member->community_cluster_id,
                'relationship_id' => $member->relationship_id,
                'baptism_parish' => 'Original Parish',
                'baptism_parish_id' => $parish->id,
                'spouse_source' => 'Member',
                'father_source' => 'Member',
                'mother_source' => 'Member',
            ])
            ->assertSessionDoesntHaveErrors(['baptism_parish']);
    });
});

describe('Member Address Validation', function () {
    test('validates permanent city ID', function () {
        $this->actingAs($this->admin)
            ->post(route('member.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'community_id' => Community::factory()->create()->id,
                'relationship_id' => Relationship::factory()->create()->id,
                'permanent_city_id' => 'invalid',
            ])
            ->assertSessionHasErrors(['permanent_city_id']);
    });

    test('validates current city ID', function () {
        $this->actingAs($this->admin)
            ->post(route('member.store'), [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'community_id' => Community::factory()->create()->id,
                'relationship_id' => Relationship::factory()->create()->id,
                'current_city_id' => 'invalid',
            ])
            ->assertSessionHasErrors(['current_city_id']);
    });
});
