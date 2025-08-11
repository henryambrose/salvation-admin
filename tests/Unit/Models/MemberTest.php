<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MemberTest extends TestCase
{
    public function test_member_can_be_created_with_real_data()
    {
        // Test that our real data member was created correctly
        $this->assertDatabaseHas('members', [
            'first_name' => 'Xavier',
            'last_name' => 'Mendonca',
            'email' => 'e0000014@olscemail.com',
            'aadhar' => '424252520014',
            'family_no' => 'SAL-001',
        ]);
    }

    public function test_member_has_correct_relationships()
    {
        // Test that relationships are loaded correctly
        $member = Member::with(['gender', 'community', 'relationship'])->find($this->member1->id);
        echo $member->gender_id;
        $this->assertNotNull($member->gender);
        $this->assertNotNull($member->community);
        $this->assertNotNull($member->relationship);
        
        $this->assertEquals('Male', $member->gender->name);
        $this->assertEquals('Test Community', $member->community->name);
        $this->assertEquals('Member', $member->relationship->name);
    }

    public function test_member_search_scope_works()
    {
        // Test the search scope with real data
        $searchResults = Member::search('Xavier')->get();
        
        $this->assertCount(1, $searchResults);
        $this->assertEquals('Xavier', $searchResults->first()->first_name);
    }

    public function test_member_by_community_scope_works()
    {
        // Fix: Use community1 instead of community
        $communityMembers = Member::byCommunity($this->community->id)->get();
        
        $this->assertCount(2, $communityMembers);
        $this->assertTrue($communityMembers->contains('first_name', 'Xavier'));
        $this->assertTrue($communityMembers->contains('first_name', 'Theresa'));
    }

    public function test_member_full_name_attribute()
    {
        $member = Member::find($this->member1->id);
        
        $this->assertEquals('Xavier Mendonca', $member->full_name);
    }

    public function test_member_uid_attribute()
    {
        $member = Member::find($this->member1->id);
        
        $this->assertEquals('M-' . $member->id, $member->uid);
    }

    public function test_member_data_creation_debug()
    {
        // Debug: Check what's in the database
        $member = Member::find($this->member1->id);
        
        echo "\n=== Debug Info ===\n";
        echo "Member ID: " . $member->id . "\n";
        echo "Member gender_id: " . $member->gender_id . "\n";
        echo "Relationship ID: " . $member->relationship_id . "\n";
        echo "community_id: " . $member->community_id . "\n";
        echo "Member first_name: " . $member->first_name . "\n";
        echo "Gender exists: " . ($this->gender ? 'Yes' : 'No') . "\n";
        echo "Gender ID: " . ($this->gender ? $this->gender->id : 'N/A') . "\n";
        echo "==================\n";
        
        // This test should always pass and give us debug info
        $this->assertTrue(true);
    }
}
