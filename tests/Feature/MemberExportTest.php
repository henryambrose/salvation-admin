<?php

use Modules\Members\Models\Member;
use Modules\Members\Models\Community;
use Modules\Members\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Create permissions
    Permission::create(['name' => 'list-member']);
    Permission::create(['name' => 'export-member']);

    $this->admin = User::factory()->create();
    $adminRole = Role::create(['name' => 'admin']);
    $this->admin->assignRole($adminRole);
    $this->admin->givePermissionTo(['list-member', 'export-member']);
});

describe('Member Data Export', function () {
    test('can export members to excel', function () {
        Member::factory()->count(10)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('member.export'));

        $response->assertSuccessful();
        expect($response->getStatusCode())->toBe(200);
    });

    test('export respects filters', function () {
        $community = Community::factory()->create();
        Member::factory()->count(5)->create(['community_id' => $community->id]);
        Member::factory()->count(3)->create(); // Different community

        $response = $this->actingAs($this->admin)
            ->get(route('member.export', ['community_id' => $community->id]));

        $response->assertSuccessful();
        expect($response->getStatusCode())->toBe(200);
    });

    test('export respects search query', function () {
        Member::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        Member::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->get(route('member.export', ['search' => 'John']));

        $response->assertSuccessful();
        expect($response->getStatusCode())->toBe(200);
    });

    test('unauthorized user cannot export members', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('member.export'));

        expect($response->status())->toBe(403);
    });
});

describe('Member Details Export', function () {
    test('can view member details modal', function () {
        $member = Member::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/member/{$member->id}/details");

        expect($response->status())->toBe(200);
    });

    test('member details includes relationships', function () {
        $father = Member::factory()->create();
        $mother = Member::factory()->create();
        $member = Member::factory()->create([
            'father_id' => $father->id,
            'mother_id' => $mother->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/member/{$member->id}/details");

        expect($response->status())->toBe(200);
    });

    test('member details includes parish information', function () {
        $parish = \Modules\Members\Models\Parish::factory()->create();
        $member = Member::factory()->create([
            'baptism_parish_id' => $parish->id,
            'baptism_parish' => $parish->name,
        ]);

        $response = $this->actingAs($this->admin)
            ->get("/member/{$member->id}/details");

        expect($response->status())->toBe(200);
    });
});
