<?php

use Modules\Members\Models\Member;
use Modules\Members\Models\Community;

test('member can have family members', function () {
    $familyNo = 'SAL-001';
    $father = Member::factory()->create(['family_no' => $familyNo]);
    $mother = Member::factory()->create(['family_no' => $familyNo]);
    $child = Member::factory()->create([
        'family_no' => $familyNo,
        'father_id' => $father->id,
        'mother_id' => $mother->id,
    ]);

    $familyMembers = Member::where('family_no', $familyNo)->get();

    expect($familyMembers)->toHaveCount(3)
        ->and($child->father->id)->toBe($father->id)
        ->and($child->mother->id)->toBe($mother->id);
});

test('member can have children', function () {
    $parent = Member::factory()->create();
    $children = Member::factory()->count(3)->create(['father_id' => $parent->id]);

    $parentChildren = Member::where('father_id', $parent->id)->get();

    expect($parentChildren)->toHaveCount(3);
});

test('member spouse relationship is bidirectional', function () {
    $husband = Member::factory()->create();
    $wife = Member::factory()->create(['spouse_id' => $husband->id]);
    $husband->spouse_id = $wife->id;
    $husband->save();

    expect($husband->spouse->id)->toBe($wife->id)
        ->and($wife->spouse->id)->toBe($husband->id);
});

test('members in same community can be fetched', function () {
    $community = Community::factory()->create();
    Member::factory()->count(5)->create(['community_id' => $community->id]);
    Member::factory()->count(3)->create(); // Different community

    $communityMembers = Member::where('community_id', $community->id)->get();

    expect($communityMembers)->toHaveCount(5);
});

test('members can be filtered by family number', function () {
    $familyNo = 'SAL-123';
    Member::factory()->count(4)->create(['family_no' => $familyNo]);
    Member::factory()->count(2)->create(['family_no' => 'SAL-456']);

    $familyMembers = Member::where('family_no', $familyNo)->get();

    expect($familyMembers)->toHaveCount(4);
});

test('member can have external spouse', function () {
    $member = Member::factory()->create([
        'spouse_source' => 'External',
    ]);

    // External spouse would be in external_members table
    expect($member->spouse_source)->toBe('External');
});

test('member baptism parish relationship works', function () {
    $parish = \Modules\Members\Models\Parish::factory()->create();
    $member = Member::factory()->create([
        'baptism_parish_id' => $parish->id,
        'baptism_parish' => $parish->name,
    ]);

    expect($member->baptismParish)->not->toBeNull()
        ->and($member->baptismParish->id)->toBe($parish->id);
});

test('member confirmation parish relationship works', function () {
    $parish = \Modules\Members\Models\Parish::factory()->create();
    $member = Member::factory()->create([
        'confirmation_parish_id' => $parish->id,
        'confirmation_parish' => $parish->name,
    ]);

    expect($member->confirmationParish)->not->toBeNull()
        ->and($member->confirmationParish->id)->toBe($parish->id);
});

test('member marriage parish relationship works', function () {
    $parish = \Modules\Members\Models\Parish::factory()->create();
    $member = Member::factory()->create([
        'marriage_parish_id' => $parish->id,
        'marriage_parish' => $parish->name,
    ]);

    expect($member->marriageParish)->not->toBeNull()
        ->and($member->marriageParish->id)->toBe($parish->id);
});

test('member address relationships work', function () {
    $city = \Modules\Members\Models\City::factory()->create();
    $state = \Modules\Members\Models\State::factory()->create();
    $country = \Modules\Members\Models\Country::factory()->create();

    $member = Member::factory()->create([
        'permanent_city_id' => $city->id,
        'permanent_state_id' => $state->id,
        'permanent_country_id' => $country->id,
    ]);

    expect($member->permanentCity)->not->toBeNull()
        ->and($member->permanentState)->not->toBeNull()
        ->and($member->permanentCountry)->not->toBeNull();
});
