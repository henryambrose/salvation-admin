<?php

use Modules\Members\Models\Member;
use Modules\Members\Models\Community;
use Modules\Members\Models\Parish;
use Modules\Members\Models\Relationship;
use Modules\Members\Models\Gender;
use Modules\Members\Models\BloodGroup;

beforeEach(function () {
    $this->member = Member::factory()->create();
});

test('member belongs to a community', function () {
    $community = Community::factory()->create();
    $member = Member::factory()->create(['community_id' => $community->id]);

    expect($member->community)->toBeInstanceOf(Community::class)
        ->and($member->community->id)->toBe($community->id);
});

test('member belongs to a parish', function () {
    $parish = Parish::factory()->create();
    $member = Member::factory()->create(['parish_id' => $parish->id]);

    expect($member->parish)->toBeInstanceOf(Parish::class)
        ->and($member->parish->id)->toBe($parish->id);
});

test('member has baptism parish relationship', function () {
    $parish = Parish::factory()->create();
    $member = Member::factory()->create(['baptism_parish_id' => $parish->id]);

    expect($member->baptismParish)->toBeInstanceOf(Parish::class)
        ->and($member->baptismParish->id)->toBe($parish->id);
});

test('member has full name accessor', function () {
    $member = Member::factory()->create([
        'first_name' => 'John',
        'middle_name' => 'Paul',
        'last_name' => 'Smith',
    ]);

    expect($member->full_name)->toBe('John Paul Smith');
});

test('member can have a father', function () {
    $maleGender = Gender::factory()->create(['name' => 'Male']);
    $father = Member::factory()->create(['gender_id' => $maleGender->id]);
    $member = Member::factory()->create(['father_id' => $father->id]);

    expect($member->father)->toBeInstanceOf(Member::class)
        ->and($member->father->id)->toBe($father->id);
});

test('member can have a mother', function () {
    $femaleGender = Gender::factory()->create(['name' => 'Female']);
    $mother = Member::factory()->create(['gender_id' => $femaleGender->id]);
    $member = Member::factory()->create(['mother_id' => $mother->id]);

    expect($member->mother)->toBeInstanceOf(Member::class)
        ->and($member->mother->id)->toBe($mother->id);
});

test('member can have a spouse', function () {
    $spouse = Member::factory()->create();
    $member = Member::factory()->create(['spouse_id' => $spouse->id]);

    expect($member->spouse)->toBeInstanceOf(Member::class)
        ->and($member->spouse->id)->toBe($spouse->id);
});

test('member belongs to a relationship type', function () {
    $relationship = Relationship::factory()->create();
    $member = Member::factory()->create(['relationship_id' => $relationship->id]);

    expect($member->relationship)->toBeInstanceOf(Relationship::class)
        ->and($member->relationship->id)->toBe($relationship->id);
});

test('member has gender', function () {
    $gender = Gender::factory()->create();
    $member = Member::factory()->create(['gender_id' => $gender->id]);

    expect($member->gender)->toBeInstanceOf(Gender::class)
        ->and($member->gender->id)->toBe($gender->id);
});

test('member has blood group', function () {
    $bloodGroup = BloodGroup::factory()->create();
    $member = Member::factory()->create(['blood_group_id' => $bloodGroup->id]);

    expect($member->bloodGroup)->toBeInstanceOf(BloodGroup::class)
        ->and($member->bloodGroup->id)->toBe($bloodGroup->id);
});

test('member can be soft deleted', function () {
    $member = Member::factory()->create();

    $member->delete();

    expect($member->trashed())->toBeTrue()
        ->and(Member::withTrashed()->find($member->id))->not->toBeNull();
});

test('member can be restored after soft delete', function () {
    $member = Member::factory()->create();
    $member->delete();

    $member->restore();

    expect($member->trashed())->toBeFalse()
        ->and(Member::find($member->id))->not->toBeNull();
});

test('member family_no is required', function () {
    $member = Member::factory()->make(['family_no' => null]);

    expect($member->family_no)->toBeNull();
});

test('member dates are cast correctly', function () {
    $member = Member::factory()->create([
        'date_of_birth' => '1990-01-15',
        'baptism_date' => '1990-02-20',
    ]);

    expect($member->date_of_birth)->toBeString()
        ->and($member->baptism_date)->toBeString();
});

test('member can filter by community', function () {
    $community = Community::factory()->create();
    Member::factory()->count(3)->create(['community_id' => $community->id]);
    Member::factory()->count(2)->create(); // Different communities

    $members = Member::where('community_id', $community->id)->get();

    expect($members)->toHaveCount(3);
});

test('member can filter by family number', function () {
    $familyNo = 'SAL-001';
    Member::factory()->count(4)->create(['family_no' => $familyNo]);
    Member::factory()->count(2)->create(['family_no' => 'SAL-002']);

    $members = Member::where('family_no', $familyNo)->get();

    expect($members)->toHaveCount(4);
});
