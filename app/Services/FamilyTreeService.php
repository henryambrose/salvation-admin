<?php

namespace App\Services;

use App\Models\Member;
use App\Models\ExternalMember;
use App\Models\FamilyLink;
use App\Models\Relationship;
use Illuminate\Support\Collection;

class FamilyTreeService
{
    /**
     * Age threshold configuration for relationship suggestions
     */
    private const AGE_THRESHOLDS = [
        'sibling' => ['min' => 0, 'max' => 15],
        'parent_child' => ['min' => 15, 'max' => 40],
        'grandparent_grandchild' => ['min' => 40, 'max' => 80],
        'great_grandparent' => ['min' => 80, 'max' => 120]
    ];

    /**
     * Get complete family tree for a member
     */
    public function getFamilyTree(Member $member, int $maxGenerations = 3): array
    {
        // Debug: Check if there are any family links for this member
        $totalLinks = FamilyLink::where('member_id', $member->id)
            ->orWhere('related_member_id', $member->id)
            ->count();
            
        // Get the family head for reference
        $familyHead = $this->getFamilyHead($member->family_no);
        $headInfo = null;
        if ($familyHead) {
            $headInfo = [
                'id' => $familyHead->id,
                'name' => $familyHead->full_name,
                'gender' => $familyHead->gender?->name ?? 'Unknown',
                'is_current_member' => $member->id === $familyHead->id,
                'relationship_to_head' => $member->id !== $familyHead->id ? $this->calculateRelationshipToHead($member, $familyHead) : 'Self'
            ];
        }
            
        $tree = [
            'member' => $this->formatMember($member),
            'familyHead' => $headInfo,
            'parents' => $this->getParents($member),
            'spouse' => $this->getSpouse($member),
            'children' => $this->getChildren($member),
            'siblings' => $this->getSiblings($member),
            'grandparents' => $this->getGrandparents($member),
            'grandchildren' => $this->getGrandchildren($member),
            'relationships' => $this->getAllRelationships($member),
                         'familyMembers' => $this->getFamilyMembers($member),
             'externalMembers' => $this->getExternalMembers($member),
                         'debug' => [
                 'totalFamilyLinks' => $totalLinks,
                 'familyNo' => $member->family_no,
                 'familyMembersCount' => $member->family_no ? Member::where('family_no', $member->family_no)->count() : 0,
                 'memberGender' => $member->gender?->name ?? 'Unknown',
                 'memberAge' => $member->date_of_birth ? \Carbon\Carbon::parse($member->date_of_birth)->age : 'Unknown',
                 'headCentered' => true,
                 'approach' => 'dynamic_relationship_calculation',
                 'currentMemberAsCenter' => true
             ]
        ];

        return $tree;
    }

    /**
     * Get family members with dynamic relationship calculation based on current member
     */
    public function getFamilyMembers(Member $member): array
    {
        if (!$member->family_no) {
            return [];
        }

        $familyMembers = Member::where('family_no', $member->family_no)
            ->where('id', '!=', $member->id) // Exclude the current member
            ->with(['gender', 'community', 'relationship'])
            ->get();

        return $familyMembers->map(function ($familyMember) use ($member) {
            // Calculate dynamic relationship based on current member as center point
            $dynamicRelationship = $this->calculateDynamicRelationship($member, $familyMember);
            
            // Debug logging for specific case
            if ($member->member_no === 'SAL-002' || $familyMember->id === 5) {
                \Log::info("Dynamic relationship calculation for SAL-002 or member ID 5", [
                    'current_member_id' => $member->id,
                    'current_member_name' => $member->full_name,
                    'current_member_no' => $member->member_no,
                    'family_member_id' => $familyMember->id,
                    'family_member_name' => $familyMember->full_name,
                    'family_member_no' => $familyMember->member_no,
                    'family_member_gender' => $familyMember->gender?->name ?? 'Unknown',
                    'family_member_relationship_id' => $familyMember->relationship_id,
                    'family_member_relationship_name' => $familyMember->relationship?->name ?? 'Unknown',
                    'calculated_dynamic_relationship' => $dynamicRelationship
                ]);
            }
            
            return [
                'member' => $this->formatMember($familyMember),
                'relationship' => $dynamicRelationship,
                'hasDefinedRelationship' => true, // Always true since we're using dynamic calculation
                'suggestedRelationship' => null, // No suggestions in dynamic approach
                'is_external' => false
            ];
        })->toArray();
    }

    /**
     * Get external members for the family
     */
    public function getExternalMembers(Member $member): array
    {
        if (!$member->family_no) {
            return [];
        }

        $externalMembers = ExternalMember::where('family_no', $member->family_no)
            ->with(['relationship'])
            ->get();

        return $externalMembers->map(function ($externalMember) use ($member) {
            // Check if there's an existing relationship
            $existingRelationship = $this->getExistingExternalRelationship($member, $externalMember);
            
            return [
                'member' => $this->formatExternalMember($externalMember),
                'relationship' => $existingRelationship ? $existingRelationship->name : 'External Family Member',
                'hasDefinedRelationship' => $existingRelationship ? true : false,
                'is_external' => true
            ];
        })->toArray();
    }

    /**
     * Get suggested relationships for family members (only for members without existing relationships)
     */
    public function getSuggestedRelationships(Member $member): array
    {
        if (!$member->family_no) {
            return [];
        }

        $familyMembers = Member::where('family_no', $member->family_no)
            ->where('id', '!=', $member->id)
            ->with(['gender', 'relationship'])
            ->get();

        $suggestions = [];

        foreach ($familyMembers as $familyMember) {
            // Only suggest if no relationship exists (head-centered approach)
            if (!$this->getExistingRelationship($member, $familyMember)) {
                $suggestion = $this->suggestRelationship($member, $familyMember);
                if ($suggestion) {
                    // Validate that the suggestion is gender-appropriate
                    $familyMemberGender = $familyMember->gender?->name ?? '';
                    $memberGender = $member->gender?->name ?? '';
                    
                    // Double-check gender appropriateness
                    $isAppropriate = $this->isGenderAppropriate($suggestion, $familyMemberGender);
                    
                    // Additional validation for spouse relationships
                    if (in_array($suggestion, ['Husband', 'Wife'])) {
                        if ($suggestion === 'Husband' && $memberGender !== 'Male') {
                            $isAppropriate = false; // Only males can be husbands
                        } elseif ($suggestion === 'Wife' && $memberGender !== 'Female') {
                            $isAppropriate = false; // Only females can be wives
                        }
                    }
                    
                    if ($isAppropriate) {
                        $suggestions[] = [
                            'member' => $this->formatMember($familyMember),
                            'suggestedRelationship' => $suggestion,
                            'confidence' => $this->getSuggestionConfidence($member, $familyMember),
                            'is_external' => false,
                            'headCentered' => true
                        ];
                    } else {
                        // Debug logging for rejected suggestions
                        if ($member->id === 5 || $familyMember->id === 5) {
                            \Log::info("Rejected suggestion for member ID 5", [
                                'member_id' => $member->id,
                                'member_name' => $member->full_name,
                                'member_gender' => $memberGender,
                                'family_member_id' => $familyMember->id,
                                'family_member_name' => $familyMember->full_name,
                                'family_member_gender' => $familyMemberGender,
                                'suggestion' => $suggestion,
                                'is_appropriate' => $isAppropriate,
                                'reason' => 'Gender inappropriate for head-centered suggestion'
                            ]);
                        }
                    }
                }
            }
        }

        return $suggestions;
    }

    /**
     * Get existing relationship between two members
     */
    private function getExistingRelationship(Member $member1, Member $member2): ?FamilyLink
    {
        // Check both directions for existing relationships
        $relationship = FamilyLink::where('member_id', $member1->id)
            ->where('related_member_id', $member2->id)
            ->with('relationship')
            ->first();
            
        if (!$relationship) {
            // Check reverse direction
            $relationship = FamilyLink::where('member_id', $member2->id)
                ->where('related_member_id', $member1->id)
                ->with('relationship')
                ->first();
        }
        
        // Debug logging for specific case
        if ($member1->member_no === 'SAL-002' || $member2->member_no === 'SAL-002' || $member1->id === 5 || $member2->id === 5) {
            \Log::info("Debugging relationship for SAL-002 or member ID 5", [
                'member1_id' => $member1->id,
                'member1_name' => $member1->full_name,
                'member1_no' => $member1->member_no,
                'member2_id' => $member2->id,
                'member2_name' => $member2->full_name,
                'member2_no' => $member2->member_no,
                'relationship_found' => $relationship ? true : false,
                'relationship_name' => $relationship ? $relationship->relationship->name : 'null',
                'relationship_direction' => $relationship ? ($relationship->member_id === $member1->id ? 'direct' : 'reverse') : 'none'
            ]);
        }
        
        return $relationship;
    }

    /**
     * Get existing relationship between a member and external member
     */
    private function getExistingExternalRelationship(Member $member, ExternalMember $externalMember): ?FamilyLink
    {
        return FamilyLink::where('member_id', $member->id)
            ->where('related_external_member_id', $externalMember->id)
            ->with('relationship')
            ->first();
    }

    /**
     * Get the head of the family (person with relationship_id = "Head" or oldest person)
     */
    private function getFamilyHead(string $familyNo): ?Member
    {
        // First, try to find someone with relationship_id = "Head"
        $head = Member::where('family_no', $familyNo)
            ->whereHas('relationship', function ($query) {
                $query->where('name', 'Head');
            })
            ->with(['gender'])
            ->first();

        if ($head) {
            // Debug logging for head found
            \Log::info("Found family head", [
                'family_no' => $familyNo,
                'head_id' => $head->id,
                'head_name' => $head->full_name,
                'head_gender' => $head->gender?->name ?? 'Unknown',
                'head_type' => 'designated_head'
            ]);
            return $head;
        }

        // If no head found, return the oldest person
        $oldestPerson = Member::where('family_no', $familyNo)
            ->whereNotNull('date_of_birth')
            ->with(['gender'])
            ->orderBy('date_of_birth', 'asc')
            ->first();

        if ($oldestPerson) {
            // Debug logging for oldest person as head
            \Log::info("Using oldest person as family head", [
                'family_no' => $familyNo,
                'head_id' => $oldestPerson->id,
                'head_name' => $oldestPerson->full_name,
                'head_gender' => $oldestPerson->gender?->name ?? 'Unknown',
                'head_type' => 'oldest_person',
                'date_of_birth' => $oldestPerson->date_of_birth
            ]);
        }

        return $oldestPerson;
    }

    /**
     * Suggest relationship based on head-centered logic
     */
    public function suggestRelationship(Member $member1, Member $member2): ?string
    {
        if (!$member1->family_no || $member1->family_no !== $member2->family_no) {
            return null;
        }

        // Get the family head (or oldest person if no head)
        $familyHead = $this->getFamilyHead($member1->family_no);
        if (!$familyHead) {
            return null;
        }

        // Debug logging for member ID 5
        if ($member1->id === 5 || $member2->id === 5) {
            \Log::info("Head-centered suggestion for member ID 5", [
                'member1_id' => $member1->id,
                'member1_name' => $member1->full_name,
                'member1_gender' => $member1->gender?->name ?? 'Unknown',
                'member2_id' => $member2->id,
                'member2_name' => $member2->full_name,
                'member2_gender' => $member2->gender?->name ?? 'Unknown',
                'head_id' => $familyHead->id,
                'head_name' => $familyHead->full_name,
                'head_gender' => $familyHead->gender?->name ?? 'Unknown'
            ]);
        }

        // Calculate relationships relative to the head
        $member1ToHead = $this->calculateRelationshipToHead($member1, $familyHead);
        $member2ToHead = $this->calculateRelationshipToHead($member2, $familyHead);

        // If either member is the head, use direct head-based logic
        if ($member1->id === $familyHead->id) {
            return $this->suggestRelationshipFromHead($member1, $member2);
        }
        if ($member2->id === $familyHead->id) {
            return $this->suggestRelationshipToHead($member1, $member2);
        }

        // Both members are not the head - calculate relationship between them
        return $this->calculateRelationshipBetweenMembers($member1, $member2, $familyHead);
    }

    /**
     * Suggest relationship from head's perspective (head → other member)
     */
    private function suggestRelationshipFromHead(Member $head, Member $otherMember): ?string
    {
        $ageDiff = $this->getAgeDifference($head, $otherMember);
        $otherGender = $otherMember->gender?->name ?? '';

        // Head is older than other member
        if ($ageDiff > 0) {
            if ($ageDiff >= self::AGE_THRESHOLDS['great_grandparent']['min']) {
                return $otherGender === 'Male' ? 'Great Grand Son' : 'Great Grand Daughter';
            } elseif ($ageDiff >= self::AGE_THRESHOLDS['grandparent_grandchild']['min']) {
                return $otherGender === 'Male' ? 'Grand Son' : 'Grand Daughter';
            } elseif ($ageDiff >= self::AGE_THRESHOLDS['parent_child']['min']) {
                return $otherGender === 'Male' ? 'Son' : 'Daughter';
            } elseif ($ageDiff <= self::AGE_THRESHOLDS['sibling']['max']) {
                return $otherGender === 'Male' ? 'Brother' : 'Sister';
            }
        }

        return null;
    }

    /**
     * Suggest relationship to head (other member → head)
     */
    private function suggestRelationshipToHead(Member $otherMember, Member $head): ?string
    {
        $ageDiff = $this->getAgeDifference($otherMember, $head);
        $headGender = $head->gender?->name ?? '';

        // Other member is younger than head
        if ($ageDiff < 0) {
            $absAgeDiff = abs($ageDiff);
            
            if ($absAgeDiff >= self::AGE_THRESHOLDS['great_grandparent']['min']) {
                return $headGender === 'Male' ? 'Great Grand Father' : 'Great Grand Mother';
            } elseif ($absAgeDiff >= self::AGE_THRESHOLDS['grandparent_grandchild']['min']) {
                return $headGender === 'Male' ? 'Grand Father' : 'Grand Mother';
            } elseif ($absAgeDiff >= self::AGE_THRESHOLDS['parent_child']['min']) {
                return $headGender === 'Male' ? 'Father' : 'Mother';
            } elseif ($absAgeDiff <= self::AGE_THRESHOLDS['sibling']['max']) {
                return $headGender === 'Male' ? 'Brother' : 'Sister';
            }
        }

        return null;
    }

    /**
     * Calculate relationship between two non-head members using head as reference
     */
    private function calculateRelationshipBetweenMembers(Member $member1, Member $member2, Member $head): ?string
    {
        $ageDiff = $this->getAgeDifference($member1, $member2);
        $gender1 = $member1->gender?->name ?? '';
        $gender2 = $member2->gender?->name ?? '';

        // Debug logging for member ID 5
        if ($member1->id === 5 || $member2->id === 5) {
            \Log::info("Calculating relationship between members", [
                'member1_id' => $member1->id,
                'member1_name' => $member1->full_name,
                'member1_gender' => $gender1,
                'member2_id' => $member2->id,
                'member2_name' => $member2->full_name,
                'member2_gender' => $gender2,
                'age_diff' => $ageDiff,
                'age_diff_abs' => abs($ageDiff)
            ]);
        }

        // Similar age - likely siblings or spouses
        if (abs($ageDiff) <= self::AGE_THRESHOLDS['sibling']['max']) {
            if ($gender1 !== $gender2) {
                // Different genders - likely spouses
                // Validate gender appropriateness before suggesting
                if ($gender1 === 'Male' && $gender2 === 'Female') {
                    return 'Husband'; // member1 is male, member2 is female
                } elseif ($gender1 === 'Female' && $gender2 === 'Male') {
                    return 'Wife'; // member1 is female, member2 is male
                }
            } else {
                // Same gender - likely siblings
                return $gender1 === 'Male' ? 'Brother' : 'Sister';
            }
        }

        // Age difference suggests parent/child relationship
        if (abs($ageDiff) >= self::AGE_THRESHOLDS['parent_child']['min'] && 
            abs($ageDiff) <= self::AGE_THRESHOLDS['parent_child']['max']) {
            
            if ($ageDiff > 0) {
                // member1 is older than member2, so member1 is parent, member2 is child
                return $gender2 === 'Male' ? 'Son' : 'Daughter';
            } else {
                // member2 is older than member1, so member2 is parent, member1 is child
                return $gender2 === 'Male' ? 'Father' : 'Mother';
            }
        }

        // Age difference suggests grandparent/grandchild relationship
        if (abs($ageDiff) >= self::AGE_THRESHOLDS['grandparent_grandchild']['min']) {
            if ($ageDiff > 0) {
                // member1 is older than member2, so member1 is grandparent, member2 is grandchild
                return $gender2 === 'Male' ? 'Grand Son' : 'Grand Daughter';
            } else {
                // member2 is older than member1, so member2 is grandparent, member1 is grandchild
                return $gender2 === 'Male' ? 'Grand Father' : 'Grand Mother';
            }
        }

        return null;
    }

    /**
     * Calculate relationship from a member to the head
     */
    private function calculateRelationshipToHead(Member $member, Member $head): ?string
    {
        $ageDiff = $this->getAgeDifference($member, $head);
        $memberGender = $member->gender?->name ?? '';
        $headGender = $head->gender?->name ?? '';

        // If member is younger than head
        if ($ageDiff < 0) {
            $absAgeDiff = abs($ageDiff);
            
            if ($absAgeDiff >= self::AGE_THRESHOLDS['great_grandparent']['min']) {
                return $headGender === 'Male' ? 'Great Grand Father' : 'Great Grand Mother';
            } elseif ($absAgeDiff >= self::AGE_THRESHOLDS['grandparent_grandchild']['min']) {
                return $headGender === 'Male' ? 'Grand Father' : 'Grand Mother';
            } elseif ($absAgeDiff >= self::AGE_THRESHOLDS['parent_child']['min']) {
                return $headGender === 'Male' ? 'Father' : 'Mother';
            } elseif ($absAgeDiff <= self::AGE_THRESHOLDS['sibling']['max']) {
                return $headGender === 'Male' ? 'Brother' : 'Sister';
            }
        }
        // If member is older than head
        elseif ($ageDiff > 0) {
            if ($ageDiff >= self::AGE_THRESHOLDS['great_grandparent']['min']) {
                return $memberGender === 'Male' ? 'Great Grand Son' : 'Great Grand Daughter';
            } elseif ($ageDiff >= self::AGE_THRESHOLDS['grandparent_grandchild']['min']) {
                return $memberGender === 'Male' ? 'Grand Son' : 'Grand Daughter';
            } elseif ($ageDiff >= self::AGE_THRESHOLDS['parent_child']['min']) {
                return $memberGender === 'Male' ? 'Son' : 'Daughter';
            } elseif ($ageDiff <= self::AGE_THRESHOLDS['sibling']['max']) {
                return $memberGender === 'Male' ? 'Brother' : 'Sister';
            }
        }
        // Same age - likely siblings
        else {
            return $memberGender === 'Male' ? 'Brother' : 'Sister';
        }

        return null;
    }

    /**
     * Get age difference between two members
     */
    private function getAgeDifference(Member $member1, Member $member2): int
    {
        if (!$member1->date_of_birth || !$member2->date_of_birth) {
            return 0;
        }

        $date1 = \Carbon\Carbon::parse($member1->date_of_birth);
        $date2 = \Carbon\Carbon::parse($member2->date_of_birth);

        return $date1->diffInYears($date2, false);
    }

    /**
     * Get confidence level for relationship suggestion
     */
    public function getSuggestionConfidence(Member $member1, Member $member2): string
    {
        $ageDiff = abs($this->getAgeDifference($member1, $member2));
        
        if ($ageDiff >= self::AGE_THRESHOLDS['grandparent_grandchild']['min']) {
            return 'high'; // Grandparent/grandchild relationship
        } elseif ($ageDiff >= self::AGE_THRESHOLDS['parent_child']['min']) {
            return 'medium'; // Parent/child relationship
        } elseif ($ageDiff <= self::AGE_THRESHOLDS['sibling']['max']) {
            return 'high'; // Sibling/spouse relationship
        } else {
            return 'low'; // Uncertain
        }
    }

    /**
     * Get reverse relationship name
     */
    private function getReverseRelationshipName(string $relationshipName): string
    {
        $reverseMap = [
            'Father' => 'Son',
            'Mother' => 'Daughter',
            'Son' => 'Father',
            'Daughter' => 'Mother',
            'Husband' => 'Wife',
            'Wife' => 'Husband',
            'Brother' => 'Brother',
            'Sister' => 'Sister',
            'Grand Father' => 'Grand Son',
            'Grand Mother' => 'Grand Daughter',
            'Grand Son' => 'Grand Father',
            'Grand Daughter' => 'Grand Mother',
            'Great Grand Father' => 'Great Grand Son',
            'Great Grand Mother' => 'Great Grand Daughter',
            'Great Grand Son' => 'Great Grand Father',
            'Great Grand Daughter' => 'Great Grand Mother',
            'Uncle' => 'Nephew',
            'Aunty' => 'Niece',
            'Nephew' => 'Uncle',
            'Niece' => 'Aunty',
            'Cousin' => 'Cousin',
            'Spouse' => 'Spouse',
            'Head' => 'Family Member',
            // In-law relationships - these are typically bidirectional
            'Daughter-in-Law' => 'Mother-in-Law',
            'Son-in-Law' => 'Father-in-Law',
            'Mother-in-Law' => 'Daughter-in-Law',
            'Father-in-Law' => 'Son-in-Law',
            'Sister-in-Law' => 'Sister-in-Law',
            'Brother-in-Law' => 'Brother-in-Law'
        ];
        
        $reverseName = $reverseMap[$relationshipName] ?? $relationshipName; // Keep original if not found
        
        // Debug logging for specific case
        if ($relationshipName === 'Mother' || $relationshipName === 'Father' || $relationshipName === 'Son' || $relationshipName === 'Daughter' || $relationshipName === 'Daughter-in-Law') {
            \Log::info("Reverse relationship mapping", [
                'original' => $relationshipName,
                'reverse' => $reverseName,
                'found_in_map' => isset($reverseMap[$relationshipName])
            ]);
        }
        
        return $reverseName;
    }

    /**
     * Validate if a relationship is gender-appropriate
     */
    public function isGenderAppropriate(string $relationshipName, string $gender): bool
    {
        $maleRelationships = [
            'Father', 'Son', 'Husband', 'Brother', 'Grand Father', 'Grand Son',
            'Great Grand Father', 'Great Grand Son', 'Uncle', 'Nephew',
            'Son-in-Law', 'Father-in-Law', 'Brother-in-Law'
        ];
        
        $femaleRelationships = [
            'Mother', 'Daughter', 'Wife', 'Sister', 'Grand Mother', 'Grand Daughter',
            'Great Grand Mother', 'Great Grand Daughter', 'Aunty', 'Niece',
            'Daughter-in-Law', 'Mother-in-Law', 'Sister-in-Law'
        ];
        
        $neutralRelationships = [
            'Cousin', 'Spouse', 'Head', 'Family Member', 'External Family Member'
        ];
        
        if (in_array($relationshipName, $neutralRelationships)) {
            return true;
        }
        
        if ($gender === 'Male') {
            return in_array($relationshipName, $maleRelationships);
        } elseif ($gender === 'Female') {
            return in_array($relationshipName, $femaleRelationships);
        }
        
        return false;
    }

    /**
     * Get parents of a member
     */
    public function getParents(Member $member): array
    {
        $parents = [];
        
        // Use the new separate parent fields
        if ($member->mother_id) {
            $mother = Member::with(['gender', 'community'])->find($member->mother_id);
            if ($mother) {
                $parents[] = [
                    'member' => $this->formatMember($mother),
                    'relationship' => 'Mother'
                ];
            }
        }
        
        if ($member->father_id) {
            $father = Member::with(['gender', 'community'])->find($member->father_id);
            if ($father) {
                $parents[] = [
                    'member' => $this->formatMember($father),
                    'relationship' => 'Father'
                ];
            }
        }
        
        // Also check family links for parent relationships (for backward compatibility)
        $linkParents = FamilyLink::where('related_member_id', $member->id)
            ->whereHas('relationship', function ($query) {
                $query->whereIn('name', ['Father', 'Mother', 'Step Mom']);
            })
            ->with(['member.gender', 'member.community', 'relationship'])
            ->get();

        foreach ($linkParents as $link) {
            if ($link->member) {
                $parents[] = [
                    'member' => $this->formatMember($link->member),
                    'relationship' => $link->relationship->name
                ];
            }
        }
        
        return $parents;
    }

    /**
     * Get spouse of a member
     */
    public function getSpouse(Member $member): ?array
    {
        // Use the new spouse_id field
        if ($member->spouse_id) {
            $spouse = Member::with(['gender', 'community'])->find($member->spouse_id);
            if ($spouse) {
                return [
                    'member' => $this->formatMember($spouse),
                    'relationship' => $member->gender?->name === 'Male' ? 'Wife' : 'Husband'
                ];
            }
        }
        
        // Also check family links for spouse relationships (for backward compatibility)
        $spouse = FamilyLink::where('member_id', $member->id)
            ->whereHas('relationship', function ($query) {
                $query->whereIn('name', ['Husband', 'Wife', 'Spouse']);
            })
            ->with(['relatedMember.gender', 'relatedMember.community', 'relationship'])
            ->first();

        if (!$spouse || !$spouse->relatedMember) {
            return null;
        }

        return [
            'member' => $this->formatMember($spouse->relatedMember),
            'relationship' => $spouse->relationship->name
        ];
    }

    /**
     * Get children of a member
     */
    public function getChildren(Member $member): array
    {
        $children = [];
        
        // Find children using the new parent fields
        $childMembers = Member::where('mother_id', $member->id)
            ->orWhere('father_id', $member->id)
            ->with(['gender', 'community'])
            ->get();
        
        foreach ($childMembers as $child) {
            $relationship = $child->gender?->name === 'Male' ? 'Son' : 'Daughter';
            $children[] = [
                'member' => $this->formatMember($child),
                'relationship' => $relationship
            ];
        }
        
        // Also check family links for children (for backward compatibility)
        $linkChildren = FamilyLink::where('member_id', $member->id)
            ->whereHas('relationship', function ($query) {
                $query->whereIn('name', ['Son', 'Daughter', 'Grand Son', 'Grand Daughter']);
            })
            ->with(['relatedMember.gender', 'relatedMember.community', 'relationship'])
            ->get();

        foreach ($linkChildren as $link) {
            if ($link->relatedMember) {
                $children[] = [
                    'member' => $this->formatMember($link->relatedMember),
                    'relationship' => $link->relationship->name
                ];
            }
        }
        
        return $children;
    }

    /**
     * Get siblings of a member
     */
    public function getSiblings(Member $member): array
    {
        $siblings = [];
        
        // Get parent IDs using the new parent fields
        $parentIds = $this->getParentIds($member);
        
        if (empty($parentIds)) {
            return [];
        }
        
        // Find siblings through parents using the new parent fields
        $siblingMembers = Member::where(function ($query) use ($parentIds) {
            $query->whereIn('mother_id', $parentIds)
                  ->orWhereIn('father_id', $parentIds);
        })
        ->where('id', '!=', $member->id)
        ->with(['gender', 'community'])
        ->get();
        
        foreach ($siblingMembers as $sibling) {
            $relationship = $sibling->gender?->name === 'Male' ? 'Brother' : 'Sister';
            $siblings[] = [
                'member' => $this->formatMember($sibling),
                'relationship' => $relationship
            ];
        }
        
        // Also check family links for siblings (for backward compatibility)
        $linkSiblings = FamilyLink::whereIn('member_id', $parentIds)
            ->whereHas('relationship', function ($query) {
                $query->whereIn('name', ['Son', 'Daughter']);
            })
            ->where('related_member_id', '!=', $member->id)
            ->with(['relatedMember.gender', 'relatedMember.community', 'relationship'])
            ->get();

        foreach ($linkSiblings as $link) {
            if ($link->relatedMember) {
                $siblings[] = [
                    'member' => $this->formatMember($link->relatedMember),
                    'relationship' => $link->relationship->name
                ];
            }
        }
        
        return $siblings;
    }

    /**
     * Get grandparents of a member
     */
    public function getGrandparents(Member $member): array
    {
        // Get parents first
        $parentIds = FamilyLink::where('related_member_id', $member->id)
            ->whereHas('relationship', function ($query) {
                $query->whereIn('name', ['Father', 'Mother', 'Step Mom']);
            })
            ->pluck('member_id');

        if ($parentIds->isEmpty()) {
            return [];
        }

        // Get grandparents through parents
        $grandparents = FamilyLink::whereIn('related_member_id', $parentIds)
            ->whereHas('relationship', function ($query) {
                $query->whereIn('name', ['Father', 'Mother', 'Grand Mother']);
            })
            ->with(['member.gender', 'member.community', 'relationship'])
            ->get();

        return $grandparents->map(function ($link) {
            return [
                'member' => $this->formatMember($link->member),
                'relationship' => $link->relationship->name
            ];
        })->toArray();
    }

    /**
     * Get grandchildren of a member
     */
    public function getGrandchildren(Member $member): array
    {
        // Get children first
        $childIds = FamilyLink::where('member_id', $member->id)
            ->whereHas('relationship', function ($query) {
                $query->whereIn('name', ['Son', 'Daughter']);
            })
            ->pluck('related_member_id');

        if ($childIds->isEmpty()) {
            return [];
        }

        // Get grandchildren through children
        $grandchildren = FamilyLink::whereIn('member_id', $childIds)
            ->whereHas('relationship', function ($query) {
                $query->whereIn('name', ['Son', 'Daughter']);
            })
            ->with(['relatedMember.gender', 'relatedMember.community', 'relationship'])
            ->get();

        return $grandchildren->filter(function ($link) {
            return $link->relatedMember !== null;
        })->map(function ($link) {
            return [
                'member' => $this->formatMember($link->relatedMember),
                'relationship' => $link->relationship->name
            ];
        })->toArray();
    }

    /**
     * Get all relationships for a member
     */
    public function getAllRelationships(Member $member): array
    {
        $relationships = FamilyLink::where('member_id', $member->id)
            ->with(['relatedMember.gender', 'relatedMember.community', 'relationship'])
            ->get();

        return $relationships->filter(function ($link) {
            return $link->relatedMember !== null;
        })->map(function ($link) {
            return [
                'member' => $this->formatMember($link->relatedMember),
                'relationship' => $link->relationship->name,
                'relationship_id' => $link->relationship_id
            ];
        })->toArray();
    }

    /**
     * Add a relationship between two members
     */
    public function addRelationship(int $memberId, int $relatedMemberId, int $relationshipId): bool
    {
        try {
            // Check if relationship already exists
            $existing = FamilyLink::where('member_id', $memberId)
                ->where('related_member_id', $relatedMemberId)
                ->first();

            if ($existing) {
                return false; // Relationship already exists
            }

            // Create the relationship
            FamilyLink::create([
                'member_id' => $memberId,
                'related_member_id' => $relatedMemberId,
                'relationship_id' => $relationshipId
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Add a relationship between a member and external member
     */
    public function addExternalRelationship(int $memberId, int $externalMemberId, int $relationshipId): bool
    {
        try {
            // Check if relationship already exists
            $existing = FamilyLink::where('member_id', $memberId)
                ->where('related_external_member_id', $externalMemberId)
                ->first();

            if ($existing) {
                return false; // Relationship already exists
            }

            // Create the relationship
            FamilyLink::create([
                'member_id' => $memberId,
                'related_external_member_id' => $externalMemberId,
                'relationship_id' => $relationshipId
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove a relationship between two members
     */
    public function removeRelationship(int $memberId, int $relatedMemberId): bool
    {
        try {
            FamilyLink::where('member_id', $memberId)
                ->where('related_member_id', $relatedMemberId)
                ->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Search members by name
     */
    public function searchMembers(string $query): array
    {
        $members = Member::where(function ($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('member_no', 'like', "%{$query}%");
        })
        ->with(['gender', 'community'])
        ->limit(10)
        ->get();

        return $members->map(function ($member) {
            return $this->formatMember($member);
        })->toArray();
    }

    /**
     * Get available relationships
     */
    public function getAvailableRelationships(): array
    {
        return Relationship::orderBy('name')->get()->toArray();
    }

    /**
     * Format member data for display
     */
    private function formatMember(Member $member): array
    {
        return [
            'id' => $member->id,
            'full_name' => $member->full_name,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'member_no' => $member->member_no,
            'family_no' => $member->family_no,
            'date_of_birth' => $member->date_of_birth,
            'gender' => $member->gender,
            'community' => $member->community,
            'relationship' => $member->relationship,
            'is_external' => false
        ];
    }

    /**
     * Format external member data for display
     */
    private function formatExternalMember(ExternalMember $member): array
    {
        return [
            'id' => $member->id,
            'full_name' => $member->full_name,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'address' => $member->address,
            'family_no' => $member->family_no,
            'relationship' => $member->relationship,
            'is_external' => true
        ];
    }

    /**
     * Calculate dynamic relationship based on current member as center
     */
    private function calculateDynamicRelationship(Member $currentMember, Member $familyMember): string
    {
        // Get all family links to understand the family structure
        $familyLinks = $this->getAllFamilyLinks($currentMember->family_no);
        
        // Debug logging for Trevin's case
        if ($currentMember->member_no === '2025-SAL-M000007') {
            \Log::info("Calculating relationship for Trevin", [
                'current_member_id' => $currentMember->id,
                'current_member_name' => $currentMember->full_name,
                'current_member_marital_status' => $currentMember->marital_status,
                'current_member_relation_member_id' => $currentMember->relation_member_id,
                'family_member_id' => $familyMember->id,
                'family_member_name' => $familyMember->full_name,
                'family_member_marital_status' => $familyMember->marital_status,
                'family_member_relation_member_id' => $familyMember->relation_member_id,
            ]);
        }
        
        // Check for spouse relationship first (highest priority)
        $spouseRelationship = $this->checkSpouseRelationship($currentMember, $familyMember);
        if ($spouseRelationship) {
            if ($currentMember->member_no === '2025-SAL-M000007') {
                \Log::info("Found spouse relationship", ['relationship' => $spouseRelationship]);
            }
            return $spouseRelationship;
        }
        
        // Check for parent-child relationship
        $parentChildRelationship = $this->checkParentChildRelationship($currentMember, $familyMember, $familyLinks);
        if ($parentChildRelationship) {
            if ($currentMember->member_no === '2025-SAL-M000007') {
                \Log::info("Found parent-child relationship", ['relationship' => $parentChildRelationship]);
            }
            return $parentChildRelationship;
        }
        
        // Check for sibling relationship
        $siblingRelationship = $this->checkSiblingRelationship($currentMember, $familyMember, $familyLinks);
        if ($siblingRelationship) {
            if ($currentMember->member_no === '2025-SAL-M000007') {
                \Log::info("Found sibling relationship", ['relationship' => $siblingRelationship]);
            }
            return $siblingRelationship;
        }
        
        // Check for in-law relationships
        $inLawRelationship = $this->checkInLawRelationship($currentMember, $familyMember, $familyLinks);
        if ($inLawRelationship) {
            if ($currentMember->member_no === '2025-SAL-M000007') {
                \Log::info("Found in-law relationship", ['relationship' => $inLawRelationship]);
            }
            return $inLawRelationship;
        }
        
        // Fallback to age-based relationship
        $ageBasedRelationship = $this->calculateAgeBasedRelationship($currentMember, $familyMember);
        if ($currentMember->member_no === '2025-SAL-M000007') {
            \Log::info("Using age-based relationship", ['relationship' => $ageBasedRelationship]);
        }
        return $ageBasedRelationship;
    }

    /**
     * Get all family links for a family
     */
    private function getAllFamilyLinks(string $familyNo): array
    {
        return FamilyLink::whereHas('member', function ($query) use ($familyNo) {
            $query->where('family_no', $familyNo);
        })->orWhereHas('relatedMember', function ($query) use ($familyNo) {
            $query->where('family_no', $familyNo);
        })->with(['member', 'relatedMember', 'relationship'])->get()->toArray();
    }

    /**
     * Check if two members are spouses
     */
    private function checkSpouseRelationship(Member $member1, Member $member2): ?string
    {
        // Check if they are spouses using the new spouse_id field
        if ($member1->spouse_id == $member2->id) {
            return $member1->gender?->name === 'Male' ? 'Wife' : 'Husband';
        }
        
        return null;
    }

    /**
     * Check parent-child relationship
     */
    private function checkParentChildRelationship(Member $currentMember, Member $familyMember, array $familyLinks): ?string
    {
        // Check if family member is current member's parent
        if ($currentMember->mother_id == $familyMember->id) {
            return 'Mother';
        }
        if ($currentMember->father_id == $familyMember->id) {
            return 'Father';
        }
        
        // Check if current member is family member's parent
        if ($familyMember->mother_id == $currentMember->id) {
            return 'Daughter';
        }
        if ($familyMember->father_id == $currentMember->id) {
            return 'Son';
        }
        
        return null;
    }

    /**
     * Check sibling relationship
     */
    private function checkSiblingRelationship(Member $currentMember, Member $familyMember, array $familyLinks): ?string
    {
        // Check if they share the same parents
        $currentMemberParents = $this->getParentIds($currentMember);
        $familyMemberParents = $this->getParentIds($familyMember);
        
        if (!empty(array_intersect($currentMemberParents, $familyMemberParents))) {
            return $familyMember->gender?->name === 'Male' ? 'Brother' : 'Sister';
        }
        
        return null;
    }

    /**
     * Check in-law relationship
     */
    private function checkInLawRelationship(Member $currentMember, Member $familyMember, array $familyLinks): ?string
    {
        // Check if family member is spouse of current member's child
        $currentMemberChildren = $this->getChildIds($currentMember);
        foreach ($currentMemberChildren as $childId) {
            $child = Member::find($childId);
            if ($child && $this->checkSpouseRelationship($child, $familyMember)) {
                return $familyMember->gender?->name === 'Male' ? 'Son-in-Law' : 'Daughter-in-Law';
            }
        }
        
        // Check if current member is spouse of family member's child
        $familyMemberChildren = $this->getChildIds($familyMember);
        foreach ($familyMemberChildren as $childId) {
            $child = Member::find($childId);
            if ($child && $this->checkSpouseRelationship($child, $currentMember)) {
                return $familyMember->gender?->name === 'Male' ? 'Father-in-Law' : 'Mother-in-Law';
            }
        }
        
        return null;
    }

    /**
     * Calculate age-based relationship as fallback
     */
    private function calculateAgeBasedRelationship(Member $currentMember, Member $familyMember): string
    {
        $ageDiff = $this->getAgeDifference($currentMember, $familyMember);
        $familyMemberGender = $familyMember->gender?->name ?? '';
        
        // Similar age - likely siblings
        if (abs($ageDiff) <= self::AGE_THRESHOLDS['sibling']['max']) {
            return $familyMemberGender === 'Male' ? 'Brother' : 'Sister';
        }
        
        // Current member is older - family member is child
        if ($ageDiff > 0) {
            return $familyMemberGender === 'Male' ? 'Son' : 'Daughter';
        }
        
        // Current member is younger - family member is parent or grandparent
        $absAgeDiff = abs($ageDiff);
        
        // Grandparent relationship (40+ years difference)
        if ($absAgeDiff >= self::AGE_THRESHOLDS['grandparent_grandchild']['min']) {
            return $familyMemberGender === 'Male' ? 'Grand Father' : 'Grand Mother';
        }
        
        // Parent relationship (15-40 years difference)
        if ($absAgeDiff >= self::AGE_THRESHOLDS['parent_child']['min']) {
            return $familyMemberGender === 'Male' ? 'Father' : 'Mother';
        }
        
        // Fallback to parent for smaller age differences
        return $familyMemberGender === 'Male' ? 'Father' : 'Mother';
    }

    /**
     * Get parent IDs for a member
     */
    private function getParentIds(Member $member): array
    {
        $parentIds = [];
        
        // Use the new separate parent fields
        if ($member->mother_id) {
            $parentIds[] = $member->mother_id;
        }
        if ($member->father_id) {
            $parentIds[] = $member->father_id;
        }
        
        // Also check family links for parent relationships (for backward compatibility)
        $linkParentIds = FamilyLink::where('related_member_id', $member->id)
            ->whereHas('relationship', function ($query) {
                $query->whereIn('name', ['Father', 'Mother']);
            })->pluck('member_id')->toArray();
        
        return array_merge($parentIds, $linkParentIds);
    }

    /**
     * Get child IDs for a member
     */
    private function getChildIds(Member $member): array
    {
        $childIds = [];
        
        // Use the new parent fields
        $childIds = Member::where('mother_id', $member->id)
            ->orWhere('father_id', $member->id)
            ->pluck('id')
            ->toArray();
        
        // Also check family links for children (for backward compatibility)
        $linkChildIds = FamilyLink::where('member_id', $member->id)
            ->whereHas('relationship', function ($query) {
                $query->whereIn('name', ['Son', 'Daughter']);
            })->pluck('related_member_id')->toArray();
        
        return array_merge($childIds, $linkChildIds);
    }
} 