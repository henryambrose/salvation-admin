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
     * Get complete family tree for a member
     */
    public function getFamilyTree(Member $member, int $maxGenerations = 3): array
    {
        // Get all family members first
        $allFamilyMembers = $this->getAllFamilyMembers($member);
        
        // Categorize them
        $categorized = $this->categorizeFamilyMembers($member, $allFamilyMembers);
        
        return [
            'member' => $this->formatMember($member),
            'parents' => $categorized['parents'],
            'spouse' => $categorized['spouse'],
            'children' => $categorized['children'],
            'siblings' => $categorized['siblings'],
            'grandparents' => $this->getGrandparents($member),
            'grandchildren' => $this->getGrandchildren($member),
            'familyMembers' => $categorized['familyMembers'],
            // Remove externalMembers since they're now included in familyMembers
        ];
    }

    /**
     * Get family members with simple relationship calculation
     */
    public function getFamilyMembers(Member $member): array
    {
        if (!$member->family_no) {
            return [];
        }

        // Get all family members except the current member
        $allFamilyMembers = Member::where('family_no', $member->family_no)
            ->where('id', '!=', $member->id)
            ->with(['gender', 'community', 'relationship'])
            ->get();

        // Get IDs to remove from family members section
        $childIds = $this->getChildIds($member);
        $spouseIds = $this->getSpouseIds($member); // New method to get all spouse IDs

        // Remove children and spouses from the main array
        $remainingFamilyMembers = $allFamilyMembers->filter(function ($familyMember) use ($childIds, $spouseIds) {
            return !in_array($familyMember->id, $childIds) && !in_array($familyMember->id, $spouseIds);
        });

        // Calculate relationships for remaining members
        return $remainingFamilyMembers->map(function ($familyMember) use ($member) {
            $relationship = $this->calculateSimpleRelationship($member, $familyMember);
            
            // Only include members with explicit relationships
            if ($relationship !== 'Family Member') {
                return [
                    'member' => $this->formatMember($familyMember),
                    'relationship' => $relationship,
                    'hasDefinedRelationship' => true,
                    'is_external' => false
                ];
            }
            
            return null;
        })->filter(function ($item) {
            return $item !== null;
        })->toArray();
    }

    /**
     * Simple relationship calculation that works for both internal and external members
     */
    private function calculateSimpleRelationship(Member $currentMember, $familyMember): string
    {
        // Handle both Member and ExternalMember objects
        $isExternal = $familyMember instanceof ExternalMember;
        
        // Direct parent-child relationships
        if ($currentMember->mother_id == $familyMember->id) {
            return 'Mother';
        }
        if ($currentMember->father_id == $familyMember->id) {
            return 'Father';
        }
        if ($familyMember->mother_id == $currentMember->id) {
            return $familyMember->gender?->name === 'Male' ? 'Son' : 'Daughter';
        }
        if ($familyMember->father_id == $currentMember->id) {
            return $familyMember->gender?->name === 'Male' ? 'Son' : 'Daughter';
        }

        // Spouse relationships
        if ($currentMember->spouse_id == $familyMember->id) {
            return $familyMember->gender?->name === 'Male' ? 'Husband' : 'Wife';
        }
        if ($familyMember->spouse_id == $currentMember->id) {
            return $familyMember->gender?->name === 'Male' ? 'Husband' : 'Wife';
        }

        // Sibling relationships (check if they share the same parent)
        if ($this->areSiblings($currentMember, $familyMember)) {
            return $familyMember->gender?->name === 'Male' ? 'Brother' : 'Sister';
        }

        // Uncle/Aunt relationships (sibling of parent)
        if ($this->isUncleOrAunt($currentMember, $familyMember)) {
            return $familyMember->gender?->name === 'Male' ? 'Uncle' : 'Aunt';
        }

        // Nephew/Niece relationships (child of sibling)
        if ($this->isNephewOrNiece($currentMember, $familyMember)) {
            return $familyMember->gender?->name === 'Male' ? 'Nephew' : 'Niece';
        }

        // In-law relationships (check this BEFORE grandparent/grandchild)
        $inLawRelationship = $this->checkInLawRelationship($currentMember, $familyMember);
        if ($inLawRelationship) {
            return $inLawRelationship;
        }

        // Grandparent relationships (check AFTER in-law relationships)
        if ($this->isGrandparent($currentMember, $familyMember)) {
            return $familyMember->gender?->name === 'Male' ? 'Grand Father' : 'Grand Mother';
        }
        
        // Grandchild relationships - only check if it's a direct grandchild (child of child)
        if ($this->isDirectGrandchild($currentMember, $familyMember)) {
            return $familyMember->gender?->name === 'Male' ? 'Grandson' : 'Granddaughter';
        }

        // Default
        return 'Family Member';
    }

    /**
     * Check for in-law relationships
     */
    private function checkInLawRelationship(Member $currentMember, $familyMember): ?string
    {
        // Check if family member is spouse of current member's child (daughter-in-law/son-in-law)
        $currentMemberChildren = $this->getChildIds($currentMember);
        foreach ($currentMemberChildren as $childId) {
            $child = Member::find($childId);
            if (!$child) {
                $child = ExternalMember::find($childId);
            }
            
            if ($child && $child->spouse_id == $familyMember->id) {
                return $familyMember->gender?->name === 'Male' ? 'Son-in-Law' : 'Daughter-in-Law';
            }
        }

        // Check if current member is spouse of family member's child (mother-in-law/father-in-law)
        $familyMemberChildren = $this->getChildIds($familyMember);
        foreach ($familyMemberChildren as $childId) {
            $child = Member::find($childId);
            if (!$child) {
                $child = ExternalMember::find($childId);
            }
            
            if ($child && $child->spouse_id == $currentMember->id) {
                return $currentMember->gender?->name === 'Male' ? 'Father-in-Law' : 'Mother-in-Law';
            }
        }

        // Check if family member is sibling of current member's spouse (sister-in-law/brother-in-law)
        if ($currentMember->spouse_id) {
            $spouse = Member::find($currentMember->spouse_id);
            if (!$spouse) {
                $spouse = ExternalMember::find($currentMember->spouse_id);
            }
            
            if ($spouse && $this->areSiblings($spouse, $familyMember)) {
                return $familyMember->gender?->name === 'Male' ? 'Brother-in-Law' : 'Sister-in-Law';
            }
        }

        // Check if current member is sibling of family member's spouse (sister-in-law/brother-in-Law)
        if ($familyMember->spouse_id) {
            $familyMemberSpouse = Member::find($familyMember->spouse_id);
            if (!$familyMemberSpouse) {
                $familyMemberSpouse = ExternalMember::find($familyMember->spouse_id);
            }
            
            if ($familyMemberSpouse && $this->areSiblings($familyMemberSpouse, $currentMember)) {
                return $currentMember->gender?->name === 'Male' ? 'Brother-in-Law' : 'Sister-in-Law';
            }
        }

        return null;
    }

    /**
     * Check if family member is grandparent of current member (works for both internal and external)
     */
    private function isGrandparent(Member $currentMember, $familyMember): bool
    {
        $currentMemberParents = $this->getParentIds($currentMember);
        
        foreach ($currentMemberParents as $parentId) {
            $parent = Member::find($parentId);
            if (!$parent) {
                // Check if it's an external member
                $parent = ExternalMember::find($parentId);
            }
            
            if ($parent && ($parent->mother_id == $familyMember->id || $parent->father_id == $familyMember->id)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if family member is a direct grandchild of current member (child of child)
     */
    private function isDirectGrandchild(Member $currentMember, $familyMember): bool
    {
        // Get current member's children
        $currentMemberChildren = $this->getChildIds($currentMember);
        
        // Check if family member is a child of any of current member's children
        foreach ($currentMemberChildren as $childId) {
            $child = Member::find($childId);
            if (!$child) {
                $child = ExternalMember::find($childId);
            }
            
            if ($child) {
                $childChildren = $this->getChildIds($child);
                if (in_array($familyMember->id, $childChildren)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Check if family member is nephew/niece of current member (child of sibling)
     */
    private function isNephewOrNiece(Member $currentMember, $familyMember): bool
    {
        // Get current member's siblings
        $siblingIds = $this->getSiblingIds($currentMember);
        
        // Check if family member is a child of any of current member's siblings
        foreach ($siblingIds as $siblingId) {
            $sibling = Member::find($siblingId);
            if (!$sibling) {
                $sibling = ExternalMember::find($siblingId);
            }
            
            if ($sibling) {
                $siblingChildren = $this->getChildIds($sibling);
                if (in_array($familyMember->id, $siblingChildren)) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Check if two members are siblings (works for both internal and external)
     */
    private function areSiblings(Member $member1, $member2): bool
    {
        $member1Parents = $this->getParentIds($member1);
        $member2Parents = $this->getParentIds($member2);
        
        return !empty(array_intersect($member1Parents, $member2Parents));
    }

    /**
     * Check if family member is uncle/aunt of current member (sibling of parent)
     */
    private function isUncleOrAunt(Member $currentMember, $familyMember): bool
    {
        // Get current member's parents
        $parentIds = $this->getParentIds($currentMember);
        
        // Check if family member is a sibling of any of current member's parents
        foreach ($parentIds as $parentId) {
            $parent = Member::find($parentId);
            if (!$parent) {
                $parent = ExternalMember::find($parentId);
            }
            
            if ($parent && $this->areSiblings($parent, $familyMember)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get external members with simple relationship calculation
     */
    public function getExternalMembers(Member $member): array
    {
        if (!$member->family_no) {
            return [];
        }

        $externalMembers = ExternalMember::where('family_no', $member->family_no)
            ->with(['relationship', 'gender'])
            ->get();

        return $externalMembers->map(function ($externalMember) use ($member) {
            // Treat external members as regular family members for relationship calculation
            $relationship = $this->calculateSimpleRelationship($member, $externalMember);
            
            return [
                'member' => $this->formatExternalMember($externalMember),
                'relationship' => $relationship,
                'hasDefinedRelationship' => true,
                'is_external' => true,
                'member_type' => 'external'
            ];
        })->toArray();
    }

    /**
     * Simple external member relationship calculation
     */
    private function calculateExternalRelationship(Member $currentMember, ExternalMember $externalMember): string
    {
        // External member is current member's parent
        if ($currentMember->father_id == $externalMember->id) {
            return 'Father';
        }
        if ($currentMember->mother_id == $externalMember->id) {
            return 'Mother';
        }

        // Current member is external member's parent
        if ($externalMember->father_id == $currentMember->id) {
            return $externalMember->gender?->name === 'Male' ? 'Son' : 'Daughter';
        }
        if ($externalMember->mother_id == $currentMember->id) {
            return $externalMember->gender?->name === 'Male' ? 'Son' : 'Daughter';
        }

        // Spouse relationships
        if ($currentMember->spouse_id == $externalMember->id) {
            return $externalMember->gender?->name === 'Male' ? 'Husband' : 'Wife';
        }
        if ($externalMember->spouse_id == $currentMember->id) {
            return $externalMember->gender?->name === 'Male' ? 'Husband' : 'Wife';
        }

        // Grandparent relationships
        if ($this->isExternalGrandparent($currentMember, $externalMember)) {
            return $externalMember->gender?->name === 'Male' ? 'Grand Father' : 'Grand Mother';
        }
        if ($this->isExternalGrandchild($currentMember, $externalMember)) {
            return $externalMember->gender?->name === 'Male' ? 'Grandson' : 'Granddaughter';
        }

        // Fallback
        if ($externalMember->relationship_id) {
            $relationship = Relationship::find($externalMember->relationship_id);
            if ($relationship) {
                return $relationship->name;
            }
        }

        return 'External Family Member';
    }

    /**
     * Check if external member is grandparent of current member
     */
    private function isExternalGrandparent(Member $currentMember, ExternalMember $externalMember): bool
    {
        $currentMemberParents = $this->getParentIds($currentMember);
        
        foreach ($currentMemberParents as $parentId) {
            $parent = Member::find($parentId);
            if ($parent && ($parent->mother_id == $externalMember->id || $parent->father_id == $externalMember->id)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if external member is grandchild of current member
     */
    private function isExternalGrandchild(Member $currentMember, ExternalMember $externalMember): bool
    {
        $externalMemberParents = [$externalMember->father_id, $externalMember->mother_id];
        
        foreach ($externalMemberParents as $parentId) {
            if ($parentId) {
                $parent = Member::find($parentId);
                if ($parent && ($parent->mother_id == $currentMember->id || $parent->father_id == $currentMember->id)) {
                    return true;
                }
            }
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
            'gender' => $member->gender,
            'community' => null, // External members don't have community
            'father' => $member->father,
            'mother' => $member->mother,
            'spouse' => $member->spouse,
            'is_external' => true,
            'member_type' => 'external'
        ];
    }

    /**
     * Get parent IDs for a member (works for both internal and external)
     */
    private function getParentIds($member): array
    {
        $parentIds = [];
        
        // Use the separate parent fields
        if ($member->mother_id) {
            $parentIds[] = $member->mother_id;
        }
        if ($member->father_id) {
            $parentIds[] = $member->father_id;
        }
        
        return $parentIds;
    }

    /**
     * Get child IDs for a member (works for both internal and external)
     */
    private function getChildIds($member): array
    {
        $childIds = [];
        
        // Use the new parent fields
        if ($member instanceof Member) {
            $childIds = Member::where('mother_id', $member->id)
                ->orWhere('father_id', $member->id)
                ->pluck('id')
                ->toArray();
            
            // Also check external members who have this member as parent
            $externalChildIds = ExternalMember::where('mother_id', $member->id)
                ->orWhere('father_id', $member->id)
                ->pluck('id')
                ->toArray();
            
            $childIds = array_merge($childIds, $externalChildIds);
        } elseif ($member instanceof ExternalMember) {
            $childIds = Member::where('mother_id', $member->id)
                ->orWhere('father_id', $member->id)
                ->pluck('id')
                ->toArray();
            
            // Also check external members
            $externalChildIds = ExternalMember::where('mother_id', $member->id)
                ->orWhere('father_id', $member->id)
                ->pluck('id')
                ->toArray();
            
            $childIds = array_merge($childIds, $externalChildIds);
        }
        
        // Also check family links for children (for backward compatibility)
        $linkChildIds = FamilyLink::where('member_id', $member->id)
            ->whereHas('relationship', function ($query) {
                $query->whereIn('name', ['Son', 'Daughter']);
            })->pluck('related_member_id')->toArray();
        
        return array_merge($childIds, $linkChildIds);
    }

    /**
     * Get sibling IDs for a member (works for both internal and external)
     */
    private function getSiblingIds($member): array
    {
        $parentIds = $this->getParentIds($member);
        
        if (empty($parentIds)) {
            return [];
        }

        // Find siblings through parents
        $siblingIds = Member::where(function ($query) use ($parentIds) {
            $query->whereIn('mother_id', $parentIds)
                  ->orWhereIn('father_id', $parentIds);
        })
        ->where('id', '!=', $member->id)
        ->pluck('id')
        ->toArray();

        // Also check external members
        $externalSiblingIds = ExternalMember::where(function ($query) use ($parentIds) {
            $query->whereIn('mother_id', $parentIds)
                  ->orWhereIn('father_id', $parentIds);
        })
        ->where('id', '!=', $member->id)
        ->pluck('id')
        ->toArray();

        return array_merge($siblingIds, $externalSiblingIds);
    }

    /**
     * Get all family members (internal and external)
     */
    private function getAllFamilyMembers(Member $member): Collection
    {
        $familyMembers = Member::where('family_no', $member->family_no)
            ->where('id', '!=', $member->id) // Filter out current member
            ->with(['gender', 'community', 'relationship'])
            ->get();

        $externalMembers = ExternalMember::where('family_no', $member->family_no)
            ->with(['gender', 'relationship'])
            ->get();

        return $familyMembers->merge($externalMembers);
    }

    /**
     * Get all spouse IDs (bidirectional) - includes external members
     */
    private function getSpouseIds(Member $member): array
    {
        $spouseIds = [];
        
        // Current member's spouse
        if ($member->spouse_id) {
            $spouseIds[] = $member->spouse_id;
        }
        
        // Members who have current member as their spouse
        $spouseIds = array_merge($spouseIds, 
            Member::where('spouse_id', $member->id)->pluck('id')->toArray()
        );
        
        // External members who have current member as their spouse
        $spouseIds = array_merge($spouseIds,
            ExternalMember::where('spouse_id', $member->id)->pluck('id')->toArray()
        );
        
        return array_unique($spouseIds);
    }

    /**
     * Get all family members and categorize them
     */
    private function categorizeFamilyMembers(Member $member, Collection $allMembers): array
    {
        $childIds = $this->getChildIds($member);
        $spouseIds = $this->getSpouseIds($member);
        $parentIds = $this->getParentIds($member);
        $siblingIds = $this->getSiblingIds($member);
        
        $categorized = [
            'parents' => [],
            'spouse' => null,
            'children' => [],
            'siblings' => [],
            'familyMembers' => []
        ];
        
        foreach ($allMembers as $familyMember) {
            $memberId = $familyMember->id;
            
            if (in_array($memberId, $parentIds)) {
                $categorized['parents'][] = [
                    'member' => $familyMember instanceof ExternalMember 
                        ? $this->formatExternalMember($familyMember) 
                        : $this->formatMember($familyMember),
                    'relationship' => $member->mother_id == $memberId ? 'Mother' : 'Father'
                ];
            }
            elseif (in_array($memberId, $spouseIds)) {
                $categorized['spouse'] = [
                    'member' => $familyMember instanceof ExternalMember 
                        ? $this->formatExternalMember($familyMember) 
                        : $this->formatMember($familyMember),
                    'relationship' => $familyMember->gender?->name === 'Male' ? 'Husband' : 'Wife'
                ];
            }
            elseif (in_array($memberId, $childIds)) {
                $categorized['children'][] = [
                    'member' => $familyMember instanceof ExternalMember 
                        ? $this->formatExternalMember($familyMember) 
                        : $this->formatMember($familyMember),
                    'relationship' => $familyMember->gender?->name === 'Male' ? 'Son' : 'Daughter'
                ];
            }
            elseif (in_array($memberId, $siblingIds)) {
                $categorized['siblings'][] = [
                    'member' => $familyMember instanceof ExternalMember 
                        ? $this->formatExternalMember($familyMember) 
                        : $this->formatMember($familyMember),
                    'relationship' => $familyMember->gender?->name === 'Male' ? 'Brother' : 'Sister'
                ];
            }
            else {
                // Only members not in other categories
                $relationship = $this->calculateSimpleRelationship($member, $familyMember);
                if ($relationship !== 'Family Member') {
                    $categorized['familyMembers'][] = [
                        'member' => $familyMember instanceof ExternalMember 
                            ? $this->formatExternalMember($familyMember) 
                            : $this->formatMember($familyMember),
                        'relationship' => $relationship,
                        'hasDefinedRelationship' => true,
                        'is_external' => $familyMember instanceof ExternalMember
                    ];
                }
            }
        }
        
        return $categorized;
    }
} 