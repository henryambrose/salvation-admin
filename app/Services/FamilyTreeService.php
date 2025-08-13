<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Relationship;
use App\Models\UnifiedPerson; // Add this import

class FamilyTreeService
{
    public function getFamilyTree(UnifiedPerson $person): array
    {
        $familyMembers = $this->getFamilyMembers($person);
        // $externalMembers = $this->getExternalMembers($person);

        return [
            'person' => $this->formatUnifiedPerson($person),
            'parents' => $this->getParents($person),
            'spouse' => $this->getSpouse($person),
            'children' => $this->getChildren($person),
            'siblings' => $this->getSiblings($person),
            'grandparents' => $this->getGrandparents($person),
            'greatGrandparents' => $this->getGreatGrandparents($person),
            'greatGreatGrandparents' => $this->getGreatGreatGrandparents($person),
            'grandchildren' => $this->getGrandchildren($person),
            'greatGrandchildren' => $this->getGreatGrandchildren($person),
            'greatGreatGrandchildren' => $this->getGreatGreatGrandchildren($person),
            'familyMembers' => $familyMembers,
            // 'externalMembers' => $externalMembers,
        ];
    }

    public function getFamilyTreeForMember(Member $member): array
    {
        $person = UnifiedPerson::where('uid', 'M-'.$member->id)->first();

        return $person ? $this->getFamilyTree($person) : [];
    }

    public function getFamilyMembers(UnifiedPerson $person): array
    {
        $members = UnifiedPerson::where('family_no', $person->family_no)
            ->where('uid', '!=', $person->uid)
            ->get();

        $immediateIds = $this->getImmediateFamilyIds($person);

        return $members->filter(fn ($m) => ! in_array($m->uid, $immediateIds))
            ->map(fn ($m) => [
                'member' => $this->formatUnifiedPerson($m),
                'relationship' => $this->calculateRelationship($person, $m),
                'hasDefinedRelationship' => true,
                'is_external' => $m->isExternal(),
            ])->toArray();
    }

    public function getExternalMembers(UnifiedPerson $person): array
    {
        return UnifiedPerson::where('family_no', $person->family_no)
            ->where('source', 'External')
            ->get()
            ->map(fn ($m) => [
                'member' => $this->formatUnifiedPerson($m),
                'relationship' => $this->calculateRelationship($person, $m),
                'hasDefinedRelationship' => true,
                'is_external' => true,
                'member_type' => 'external',
            ])->toArray();
    }

    public function getParents(UnifiedPerson $person): array
    {
        $parents = [];
        if ($person->father) {
            $parents[] = ['member' => $this->formatUnifiedPerson($person->father), 'relationship' => 'Father'];
        }
        if ($person->mother) {
            $parents[] = ['member' => $this->formatUnifiedPerson($person->mother), 'relationship' => 'Mother'];
        }

        return $parents;
    }

    public function getSpouse(UnifiedPerson $person): ?array
    {
        \Log::info('Getting spouse for person', [
            'person_uid' => $person->uid,
            'person_name' => $person->full_name,
            'spouse_uid' => $person->spouse_uid,
            'has_spouse' => $person->spouse ? 'yes' : 'no'
        ]);
        
        return $person->spouse ? [
            'member' => $this->formatUnifiedPerson($person->spouse),
            'relationship' => $person->spouse->gender_id == 1 ? 'Husband' : 'Wife',
        ] : null;
    }

    public function getChildren(UnifiedPerson $person): array
    {
        return $person->getChildren()->map(fn ($child) => [
            'member' => $this->formatUnifiedPerson($child),
            'relationship' => $child->gender_id == 1 ? 'Son' : 'Daughter',
        ])->toArray();
    }

    public function getSiblings(UnifiedPerson $person): array
    {
        return $person->getSiblings()->map(fn ($sibling) => [
            'member' => $this->formatUnifiedPerson($sibling),
            'relationship' => $sibling->gender_id == 1 ? 'Brother' : 'Sister',
        ])->toArray();
    }

    public function getGrandparents(UnifiedPerson $person): array
    {
        $g = [];
        foreach ([$person->father, $person->mother] as $p) {
            if ($p?->father) {
                $g[] = ['member' => $this->formatUnifiedPerson($p->father), 'relationship' => 'Grand Father'];
            }
            if ($p?->mother) {
                $g[] = ['member' => $this->formatUnifiedPerson($p->mother), 'relationship' => 'Grand Mother'];
            }
        }

        return $g;
    }

    public function getGreatGrandparents(UnifiedPerson $person): array
    {
        $gg = [];
        foreach ([$person->father, $person->mother] as $parent) {
            if ($parent) {
                // Father's grandparents
                if ($parent->father?->father) {
                    $gg[] = ['member' => $this->formatUnifiedPerson($parent->father->father), 'relationship' => 'Great Grand Father'];
                }
                if ($parent->father?->mother) {
                    $gg[] = ['member' => $this->formatUnifiedPerson($parent->father->mother), 'relationship' => 'Great Grand Mother'];
                }
                // Mother's grandparents
                if ($parent->mother?->father) {
                    $gg[] = ['member' => $this->formatUnifiedPerson($parent->mother->father), 'relationship' => 'Great Grand Father'];
                }
                if ($parent->mother?->mother) {
                    $gg[] = ['member' => $this->formatUnifiedPerson($parent->mother->mother), 'relationship' => 'Great Grand Mother'];
                }
            }
        }

        return $gg;
    }

    public function getGreatGreatGrandparents(UnifiedPerson $person): array
    {
        $ggg = [];
        foreach ([$person->father, $person->mother] as $parent) {
            if ($parent) {
                // Father's great grandparents
                if ($parent->father) {
                    if ($parent->father->father?->father) {
                        $ggg[] = ['member' => $this->formatUnifiedPerson($parent->father->father->father), 'relationship' => 'Great Great Grand Father'];
                    }
                    if ($parent->father->father?->mother) {
                        $ggg[] = ['member' => $this->formatUnifiedPerson($parent->father->father->mother), 'relationship' => 'Great Great Grand Mother'];
                    }
                    if ($parent->father->mother?->father) {
                        $ggg[] = ['member' => $this->formatUnifiedPerson($parent->father->mother->father), 'relationship' => 'Great Great Grand Father'];
                    }
                    if ($parent->father->mother?->mother) {
                        $ggg[] = ['member' => $this->formatUnifiedPerson($parent->father->mother->mother), 'relationship' => 'Great Great Grand Mother'];
                    }
                }
                // Mother's great grandparents
                if ($parent->mother) {
                    if ($parent->mother->father?->father) {
                        $ggg[] = ['member' => $this->formatUnifiedPerson($parent->mother->father->father), 'relationship' => 'Great Great Grand Father'];
                    }
                    if ($parent->mother->father?->mother) {
                        $ggg[] = ['member' => $this->formatUnifiedPerson($parent->mother->father->mother), 'relationship' => 'Great Great Grand Mother'];
                    }
                    if ($parent->mother->mother?->father) {
                        $ggg[] = ['member' => $this->formatUnifiedPerson($parent->mother->mother->father), 'relationship' => 'Great Great Grand Father'];
                    }
                    if ($parent->mother->mother?->mother) {
                        $ggg[] = ['member' => $this->formatUnifiedPerson($parent->mother->mother->mother), 'relationship' => 'Great Great Grand Mother'];
                    }
                }
            }
        }

        return $ggg;
    }

    public function getGrandchildren(UnifiedPerson $person): array
    {
        $g = [];
        foreach ($person->getChildren() as $c) {
            foreach ($c->getChildren() as $gc) {
                $g[] = ['member' => $this->formatUnifiedPerson($gc), 'relationship' => $gc->gender_id == 1 ? 'Grandson' : 'Granddaughter'];
            }
        }

        return $g;
    }

    public function getGreatGrandchildren(UnifiedPerson $person): array
    {
        $gg = [];
        foreach ($person->getChildren() as $child) {
            foreach ($child->getChildren() as $grandchild) {
                foreach ($grandchild->getChildren() as $greatGrandchild) {
                    $gg[] = ['member' => $this->formatUnifiedPerson($greatGrandchild), 'relationship' => $greatGrandchild->gender_id == 1 ? 'Great Grandson' : 'Great Granddaughter'];
                }
            }
        }

        return $gg;
    }

    public function getGreatGreatGrandchildren(UnifiedPerson $person): array
    {
        $ggg = [];
        foreach ($person->getChildren() as $child) {
            foreach ($child->getChildren() as $grandchild) {
                foreach ($grandchild->getChildren() as $greatGrandchild) {
                    foreach ($greatGrandchild->getChildren() as $greatGreatGrandchild) {
                        $ggg[] = ['member' => $this->formatUnifiedPerson($greatGreatGrandchild), 'relationship' => $greatGreatGrandchild->gender_id == 1 ? 'Great Great Grandson' : 'Great Great Granddaughter'];
                    }
                }
            }
        }

        return $ggg;
    }

    public function calculateRelationship(UnifiedPerson $p1, UnifiedPerson $p2): string
    {
        if ($p1->family_no !== $p2->family_no) {
            return 'Family Member';
        }

        // ✅ Child's spouse has higher priority than any sister/brother-in-law logic
        foreach ($p1->getChildren() as $child) {
            if ($child->spouse_uid === $p2->uid || $p2->spouse_uid === $child->uid) {
                return $p2->gender_id == 1 ? 'Son-in-Law' : 'Daughter-in-Law';
            }
        }

        // ✅ Grandchild's spouse has higher priority than any sister/brother-in-law logic
        foreach ($p1->getChildren() as $child) {
            foreach ($child->getChildren() as $grandchild) {
                if ($grandchild->spouse_uid === $p2->uid || $p2->spouse_uid === $grandchild->uid) {
                    return $p2->gender_id == 1 ? 'Grandson-in-Law' : 'Granddaughter-in-Law';
                }
            }
        }

        // NEW LOGIC: Handle females who married into the family
        if ($p1->gender_id == 2 && // Female
            $p1->spouse_uid && // Has spouse
            !$p1->father_uid && // No father (not born into family)
            !$p1->mother_uid) { // No mother (not born into family)
            
            \Log::info('Female married into family - checking in-law relationships', [
                'p1' => $p1->uid,
                'p2' => $p2->uid,
                'p1_spouse_uid' => $p1->spouse_uid
            ]);
            
            // Check if p2 is her spouse
            if ($p1->spouse_uid === $p2->uid) {
                return $p2->gender_id == 1 ? 'Husband' : 'Wife';
            }
            
            // Check if p2 is her spouse's parent (Father-in-Law/Mother-in-Law)
            if ($p1->spouse) {
                if ($p1->spouse->father_uid === $p2->uid) {
                    return 'Father-in-Law';
                }
                if ($p1->spouse->mother_uid === $p2->uid) {
                    return 'Mother-in-Law';
                }
            }
            
            // Check if p2 is her spouse's sibling (Brother-in-Law/Sister-in-Law)
            if ($p1->spouse) {
                foreach ($p1->spouse->getSiblings() as $spouseSibling) {
                    if ($spouseSibling->uid === $p2->uid) {
                        return $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                    }
                }
            }
            
            // Check if p2 is her spouse's grandparent (Grandfather-in-Law/Grandmother-in-Law)
            if ($p1->spouse) {
                if ($p1->spouse->father && $p1->spouse->father->father_uid === $p2->uid) {
                    return 'Grandfather-in-Law';
                }
                if ($p1->spouse->father && $p1->spouse->father->mother_uid === $p2->uid) {
                    return 'Grandmother-in-Law';
                }
                if ($p1->spouse->mother && $p1->spouse->mother->father_uid === $p2->uid) {
                    return 'Grandfather-in-Law';
                }
                if ($p1->spouse->mother && $p1->spouse->mother->mother_uid === $p2->uid) {
                    return 'Grandmother-in-Law';
                }
            }
        }

        // ALSO CHECK: Handle when p2 is a female who married into the family
        if ($p2->gender_id == 2 && // Female
            $p2->spouse_uid && // Has spouse
            !$p2->father_uid && // No father (not born into family)
            !$p2->mother_uid) { // No mother (not born into family)
            
            \Log::info('p2 is female married into family - checking reverse in-law relationships', [
                'p1' => $p1->uid,
                'p2' => $p2->uid,
                'p2_spouse_uid' => $p2->spouse_uid
            ]);
            
            // Check if p1 is her spouse
            if ($p2->spouse_uid === $p1->uid) {
                return $p1->gender_id == 1 ? 'Husband' : 'Wife';
            }
            
            // Check if p1 is her spouse's parent (Father-in-Law/Mother-in-Law)
            if ($p2->spouse) {
                if ($p2->spouse->father_uid === $p1->uid) {
                    return 'Father-in-Law';
                }
                if ($p2->spouse->mother_uid === $p1->uid) {
                    return 'Mother-in-Law';
                }
            }
            
            // Check if p1 is her spouse's sibling (Brother-in-Law/Sister-in-Law)
            if ($p2->spouse) {
                \Log::info('Checking if p1 is p2 spouse\'s sibling', [
                    'p1_uid' => $p1->uid,
                    'p2_spouse_uid' => $p2->spouse_uid,
                    'p2_spouse_father' => $p2->spouse->father_uid,
                    'p2_spouse_mother' => $p2->spouse->mother_uid,
                    'p1_father' => $p1->father_uid,
                    'p1_mother' => $p1->mother_uid
                ]);
                
                // Direct check: Are p1 and p2's spouse siblings?
                if ($p1->father_uid && $p1->father_uid === $p2->spouse->father_uid) {
                    \Log::info('Found sibling relationship via father', [
                        'p1_uid' => $p1->uid,
                        'p2_spouse_uid' => $p2->spouse_uid,
                        'shared_father' => $p1->father_uid
                    ]);
                    return $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                }
                
                if ($p1->mother_uid && $p1->mother_uid === $p2->spouse->mother_uid) {
                    \Log::info('Found sibling relationship via mother', [
                        'p1_uid' => $p1->uid,
                        'p2_spouse_uid' => $p2->spouse_uid,
                        'shared_mother' => $p1->mother_uid
                    ]);
                    return $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                }
                
                // NEW LOGIC: Check if p1's spouse and p2's spouse are siblings
                if ($p1->spouse_uid) {
                    \Log::info('Checking if spouses are siblings', [
                        'p1_spouse_uid' => $p1->spouse_uid,
                        'p2_spouse_uid' => $p2->spouse_uid,
                        'p1_spouse_father' => $p1->spouse->father_uid ?? 'null',
                        'p1_spouse_mother' => $p1->spouse->mother_uid ?? 'null',
                        'p2_spouse_father' => $p2->spouse->father_uid ?? 'null',
                        'p2_spouse_mother' => $p2->spouse->mother_uid ?? 'null'
                    ]);
                    
                    // Check if they share the same father
                    if ($p1->spouse->father_uid && $p1->spouse->father_uid === $p2->spouse->father_uid) {
                        \Log::info('Found sibling relationship via shared father', [
                            'p1_spouse' => $p1->spouse_uid,
                            'p2_spouse' => $p2->spouse_uid,
                            'shared_father' => $p1->spouse->father_uid
                        ]);
                        return $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                    }
                    
                    // Check if they share the same mother
                    if ($p1->spouse->mother_uid && $p1->spouse->mother_uid === $p2->spouse->mother_uid) {
                        \Log::info('Found sibling relationship via shared mother', [
                            'p1_spouse' => $p1->spouse_uid,
                            'p2_spouse' => $p2->spouse_uid,
                            'shared_mother' => $p1->spouse->mother_uid
                        ]);
                        return $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                    }
                }
                
                foreach ($p2->spouse->getSiblings() as $spouseSibling) {
                    \Log::info('Checking spouse sibling', [
                        'spouse_sibling_uid' => $spouseSibling->uid,
                        'p1_uid' => $p1->uid,
                        'match' => $spouseSibling->uid === $p1->uid
                    ]);
                    
                    if ($spouseSibling->uid === $p1->uid) {
                        return $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                    }
                }
            }
            
            // Check if p1 is her spouse's grandparent (Grandfather-in-Law/Grandmother-in-Law)
            if ($p2->spouse) {
                if ($p2->spouse->father && $p2->spouse->father->father_uid === $p1->uid) {
                    return 'Grandfather-in-Law';
                }
                if ($p2->spouse->father && $p2->spouse->father->mother_uid === $p1->uid) {
                    return 'Grandmother-in-Law';
                }
                if ($p2->spouse->mother && $p2->spouse->mother->father_uid === $p1->uid) {
                    return 'Grandfather-in-Law';
                }
                if ($p2->spouse->mother && $p2->spouse->mother->mother_uid === $p1->uid) {
                    return 'Grandmother-in-Law';
                }
            }
        }

        // Direct parent/child - These should come FIRST
        if ($p1->father_uid === $p2->uid) {
            \Log::info('Relationship: Father', ['p1' => $p1->uid, 'p2' => $p2->uid]);
            return 'Father';
        }
        if ($p1->mother_uid === $p2->uid) {
            \Log::info('Relationship: Mother', ['p1' => $p1->uid, 'p2' => $p2->uid]);
            return 'Mother';
        }
        if ($p2->father_uid === $p1->uid || $p2->mother_uid === $p1->uid) {
            $relationship = $p2->gender_id == 1 ? 'Son' : 'Daughter';
            \Log::info('Relationship: ' . $relationship, ['p1' => $p1->uid, 'p2' => $p2->uid]);
            return $relationship;
        }

        // Spouse
        if ($p1->spouse_uid === $p2->uid) {
            $relationship = $p2->gender_id == 1 ? 'Husband' : 'Wife';
            \Log::info('Relationship: ' . $relationship, ['p1' => $p1->uid, 'p2' => $p2->uid]);
            return $relationship;
        }
        
        // Uncle / Aunt
        if ($this->isUncleOrAunt($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Uncle' : 'Aunt';
        }

        // Nephew / Niece
        if ($this->isNephewOrNiece($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Nephew' : 'Niece';
        }

        // Sibling - Check this BEFORE cousin to avoid conflicts
        if ($this->areSiblings($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Brother' : 'Sister';
        }

        // Grandchild - Move this BEFORE cousin to avoid conflicts
        if ($this->isGrandchild($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Grandson' : 'Granddaughter';
        }

        // Great Grandchild - Move this BEFORE cousin to avoid conflicts
        if ($this->isGreatGrandchild($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Great Grandson' : 'Great Granddaughter';
        }

        // Great Great Grandchild - Move this BEFORE cousin to avoid conflicts
        if ($this->isGreatGreatGrandchild($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Great Great Grandson' : 'Great Great Granddaughter';
        }

        // Grandparent - Move this BEFORE cousin to avoid conflicts
        if ($this->isGrandparent($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Grand Father' : 'Grand Mother';
        }

        // Great Grandparent - Move this BEFORE cousin to avoid conflicts
        if ($this->isGreatGrandparent($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Great Grand Father' : 'Great Grand Mother';
        }

        // Great Great Grandparent - Move this BEFORE cousin to avoid conflicts
        if ($this->isGreatGreatGrandparent($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Great Great Grand Father' : 'Great Great Grand Mother';
        }

        // Cousin - Move this AFTER grandparent checks
        if ($this->isCousin($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Cousin Brother' : 'Cousin Sister';
        }

        // Sibling's spouse (sister/brother-in-law) - Check this BEFORE spouse's parent
        foreach ($p1->getSiblings() as $sibling) {
            \Log::info('Checking sibling\'s spouse', [
                'p1_uid' => $p1->uid,
                'sibling_uid' => $sibling->uid,
                'sibling_spouse_uid' => $sibling->spouse_uid,
                'p2_uid' => $p2->uid,
                'match' => $sibling->spouse_uid === $p2->uid
            ]);
            
            if ($sibling->spouse_uid === $p2->uid) {
                $relationship = $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                \Log::info('Relationship: ' . $relationship, ['p1' => $p1->uid, 'p2' => $p2->uid]);
                return $relationship;
            }
        }

        // Spouse's parent → Move this BEFORE sibling's spouse to avoid conflicts
        if ($p1->spouse) {
            \Log::info('Checking spouse\'s parent', [
                'p1_uid' => $p1->uid,
                'p1_spouse_uid' => $p1->spouse_uid,
                'p2_uid' => $p2->uid,
                'spouse_father_uid' => $p1->spouse->father_uid,
                'spouse_mother_uid' => $p1->spouse->mother_uid,
            ]);
            
            if ($p1->spouse->father_uid === $p2->uid) {
                \Log::info('Relationship: Father-in-Law', ['p1' => $p1->uid, 'p2' => $p2->uid]);
                return 'Father-in-Law';
            }
            if ($p1->spouse->mother_uid === $p2->uid) {
                \Log::info('Relationship: Mother-in-Law', ['p1' => $p1->uid, 'p2' => $p2->uid]);
                return 'Mother-in-Law';
            }
        }

        // Spouse's sibling (sister/brother-in-law) - This should come after spouse's parent
        if ($p1->spouse) {
            \Log::info('Checking spouse\'s sibling', [
                'p1_uid' => $p1->uid,
                'p1_spouse_uid' => $p1->spouse_uid,
                'p2_uid' => $p2->uid,
            ]);
            
            foreach ($p1->spouse->getSiblings() as $sibling) {
                \Log::info('Checking sibling', [
                    'sibling_uid' => $sibling->uid,
                    'p2_uid' => $p2->uid,
                    'match' => $sibling->uid === $p2->uid
                ]);
                
                if ($sibling->uid === $p2->uid) {
                    $relationship = $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                    \Log::info('Relationship: ' . $relationship, ['p1' => $p1->uid, 'p2' => $p2->uid]);
                    return $relationship;
                }
            }
        }

        $inLaw = $this->checkInLawRelationship($p1, $p2);
        if ($inLaw) {
            return $inLaw;
        }

        return 'Family Member';
    }

    private function areSiblings(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        return $p1->family_no === $p2->family_no && (
            (! empty($p1->father_uid) && $p1->father_uid === $p2->father_uid) ||
            (! empty($p1->mother_uid) && $p1->mother_uid === $p2->mother_uid)
        );
    }

    private function isUncleOrAunt(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        return ($p1->father && $this->areSiblings($p1->father, $p2)) ||
               ($p1->mother && $this->areSiblings($p1->mother, $p2));
    }

    private function isNephewOrNiece(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        return $this->isUncleOrAunt($p2, $p1);
    }

    private function isCousin(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        foreach ([$p1->father, $p1->mother] as $parent) {
            if (! $parent) {
                continue;
            }
            foreach ($parent->getSiblings() as $sibling) {
                if ($sibling->uid === $p2->father_uid || $sibling->uid === $p2->mother_uid) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isGrandparent(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        return ($p1->father && ($p1->father->father_uid === $p2->uid || $p1->father->mother_uid === $p2->uid)) ||
               ($p1->mother && ($p1->mother->father_uid === $p2->uid || $p1->mother->mother_uid === $p2->uid));
    }

    private function isGreatGrandparent(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        // Check if p2 is a great grandparent of p1 (2 levels up from parents)
        foreach ([$p1->father, $p1->mother] as $parent) {
            if ($parent) {
                if ($parent->father && ($parent->father->father_uid === $p2->uid || $parent->father->mother_uid === $p2->uid)) {
                    return true;
                }
                if ($parent->mother && ($parent->mother->father_uid === $p2->uid || $parent->mother->mother_uid === $p2->uid)) {
                    return true;
                }
            }
        }
        return false;
    }

    private function isGreatGreatGrandparent(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        // Check if p2 is a great great grandparent of p1 (3 levels up from parents)
        foreach ([$p1->father, $p1->mother] as $parent) {
            if ($parent) {
                if ($parent->father) {
                    if ($parent->father->father && ($parent->father->father->father_uid === $p2->uid || $parent->father->father->mother_uid === $p2->uid)) {
                        return true;
                    }
                    if ($parent->father->mother && ($parent->father->mother->father_uid === $p2->uid || $parent->father->mother->mother_uid === $p2->uid)) {
                        return true;
                    }
                }
                if ($parent->mother) {
                    if ($parent->mother->father && ($parent->mother->father->father_uid === $p2->uid || $parent->mother->father->mother_uid === $p2->uid)) {
                        return true;
                    }
                    if ($parent->mother->mother && ($parent->mother->mother->father_uid === $p2->uid || $parent->mother->mother->mother_uid === $p2->uid)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    private function isGrandchild(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        return $this->isGrandparent($p2, $p1);
    }

    private function isGreatGrandchild(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        return $this->isGreatGrandparent($p2, $p1);
    }

    private function isGreatGreatGrandchild(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        return $this->isGreatGreatGrandparent($p2, $p1);
    }

    private function checkInLawRelationship(UnifiedPerson $p1, UnifiedPerson $p2): ?string
    {
        // Son-in-law / Daughter-in-law: if p2 is spouse of p1's child
        foreach ($p1->getChildren() as $child) {
            if (
                $child->spouse_uid === $p2->uid || // child has p2 as spouse
                $p2->spouse_uid === $child->uid    // p2 has child as spouse
            ) {
                return $p2->gender_id == 1 ? 'Son-in-Law' : 'Daughter-in-Law';
            }
        }

        // Grandson-in-law / Granddaughter-in-law: if p2 is spouse of p1's grandchild
        foreach ($p1->getGrandchildren() as $grandchild) {
            if (
                $grandchild->spouse_uid === $p2->uid || // grandchild has p2 as spouse
                $p2->spouse_uid === $grandchild->uid    // p2 has grandchild as spouse
            ) {
                return $p2->gender_id == 1 ? 'Grandson-in-Law' : 'Granddaughter-in-Law';
            }
        }

        // Great Grandson-in-Law / Great Granddaughter-in-Law: if p2 is spouse of p1's great grandchild
        foreach ($p1->getGreatGrandchildren() as $greatGrandchild) {
            if (
                $greatGrandchild->spouse_uid === $p2->uid || // great grandchild has p2 as spouse
                $p2->spouse_uid === $greatGrandchild->uid    // p2 has great grandchild as spouse
            ) {
                return $p2->gender_id == 1 ? 'Great Grandson-in-Law' : 'Great Granddaughter-in-Law';
            }
        }

        // Great Great Grandson-in-Law / Great Great Granddaughter-in-Law: if p2 is spouse of p1's great great grandchild
        foreach ($p1->getGreatGreatGrandchildren() as $greatGreatGrandchild) {
            if (
                $greatGreatGrandchild->spouse_uid === $p2->uid || // great great grandchild has p2 as spouse
                $p2->spouse_uid === $greatGreatGrandchild->uid    // p2 has great great grandchild as spouse
            ) {
                return $p2->gender_id == 1 ? 'Great Great Grandson-in-Law' : 'Great Great Granddaughter-in-Law';
            }
        }

        // Parent-in-law: if p2 is parent of p1's spouse
        if ($p1->spouse) {
            if ($p1->spouse->father_uid === $p2->uid) {
                return 'Father-in-Law';
            }
            if ($p1->spouse->mother_uid === $p2->uid) {
                return 'Mother-in-Law';
            }

            // Siblings of spouse → Brother-in-Law / Sister-in-Law
            foreach ($p1->spouse->getSiblings() as $sibling) {
                if ($sibling->uid === $p2->uid || $sibling->spouse_uid === $p2->uid) {
                    return $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                }

                // Niece/nephew from spouse's sibling
                foreach ($sibling->getChildren() as $child) {
                    if ($child->uid === $p2->uid) {
                        return $p2->gender_id == 1 ? 'Nephew' : 'Niece';
                    }
                }
            }

            // Grandparents of spouse → Grandfather-in-Law / Grandmother-in-Law
            if ($p1->spouse->father) {
                if ($p1->spouse->father->father_uid === $p2->uid) {
                    return 'Grandfather-in-Law';
                }
                if ($p1->spouse->father->mother_uid === $p2->uid) {
                    return 'Grandmother-in-Law';
                }
            }
            if ($p1->spouse->mother) {
                if ($p1->spouse->mother->father_uid === $p2->uid) {
                    return 'Grandfather-in-Law';
                }
                if ($p1->spouse->mother->mother_uid === $p2->uid) {
                    return 'Grandmother-in-Law';
                }
            }

            // Great Grandparents of spouse → Great Grandfather-in-Law / Great Grandmother-in-Law
            if ($p1->spouse->father) {
                if ($p1->spouse->father->father) {
                    if ($p1->spouse->father->father->father_uid === $p2->uid) {
                        return 'Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->father->father->mother_uid === $p2->uid) {
                        return 'Great Grandmother-in-Law';
                    }
                }
                if ($p1->spouse->father->mother) {
                    if ($p1->spouse->father->mother->father_uid === $p2->uid) {
                        return 'Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->father->mother->mother_uid === $p2->uid) {
                        return 'Great Grandmother-in-Law';
                    }
                }
            }
            if ($p1->spouse->mother) {
                if ($p1->spouse->mother->father) {
                    if ($p1->spouse->mother->father->father_uid === $p2->uid) {
                        return 'Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->mother->father->mother_uid === $p2->uid) {
                        return 'Great Grandmother-in-Law';
                    }
                }
                if ($p1->spouse->mother->mother) {
                    if ($p1->spouse->mother->mother->father_uid === $p2->uid) {
                        return 'Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->mother->mother->mother_uid === $p2->uid) {
                        return 'Great Grandmother-in-Law';
                    }
                }
            }
        }

        // In-laws via parent's remarriage / extended step-family
        foreach (['father', 'mother'] as $side) {
            $parentSpouse = $p1->{$side}?->getSpouse();
            if ($parentSpouse) {
                foreach ($parentSpouse->getSiblings() as $sibling) {
                    if ($sibling->uid === $p2->uid || $sibling->spouse_uid === $p2->uid) {
                        return $p2->gender_id == 1 ? 'Uncle' : 'Aunt';
                    }
                    foreach ($sibling->getChildren() as $child) {
                        if ($child->uid === $p2->uid) {
                            return $p2->gender_id == 1 ? 'Nephew' : 'Niece';
                        }
                    }
                }
            }
        }

        // Extended in-law: sibling's spouse's children
        foreach ($p1->getSiblings() as $sibling) {
            if ($sibling->spouse) {
                foreach ($sibling->spouse->getChildren() as $inLawChild) {
                    if ($inLawChild->uid === $p2->uid) {
                        return $p2->gender_id == 1 ? 'Nephew' : 'Niece';
                    }
                }
            }
        }

        // Extended in-law: sibling's spouse's grandchildren
        foreach ($p1->getSiblings() as $sibling) {
            if ($sibling->spouse) {
                foreach ($sibling->spouse->getChildren() as $inLawChild) {
                    foreach ($inLawChild->getChildren() as $inLawGrandchild) {
                        if ($inLawGrandchild->uid === $p2->uid) {
                            return $p2->gender_id == 1 ? 'Great Nephew' : 'Great Niece';
                        }
                    }
                }
            }
        }

        // Extended in-law: sibling's spouse's great grandchildren
        foreach ($p1->getSiblings() as $sibling) {
            if ($sibling->spouse) {
                foreach ($sibling->spouse->getChildren() as $inLawChild) {
                    foreach ($inLawChild->getChildren() as $inLawGrandchild) {
                        foreach ($inLawGrandchild->getChildren() as $inLawGreatGrandchild) {
                            if ($inLawGreatGrandchild->uid === $p2->uid) {
                                return $p2->gender_id == 1 ? 'Great Great Nephew' : 'Great Great Niece';
                            }
                        }
                    }
                }
            }
        }

        return null;
    }

    private function getImmediateFamilyIds(UnifiedPerson $person): array
    {
        $ids = array_filter([
            $person->father_uid,
            $person->mother_uid,
            $person->spouse_uid,
        ]);

        // Add children
        foreach ($person->getChildren() as $child) {
            $ids[] = $child->uid;
        }
        
        // Add siblings
        foreach ($person->getSiblings() as $sibling) {
            $ids[] = $sibling->uid;
        }
        
        // Add grandparents
        foreach ([$person->father, $person->mother] as $parent) {
            if ($parent?->father) {
                $ids[] = $parent->father->uid;
            }
            if ($parent?->mother) {
                $ids[] = $parent->mother->uid;
            }
        }
        
        // Add great grandparents
        foreach ([$person->father, $person->mother] as $parent) {
            if ($parent) {
                if ($parent->father?->father) {
                    $ids[] = $parent->father->father->uid;
                }
                if ($parent->father?->mother) {
                    $ids[] = $parent->father->mother->uid;
                }
                if ($parent->mother?->father) {
                    $ids[] = $parent->mother->father->uid;
                }
                if ($parent->mother?->mother) {
                    $ids[] = $parent->mother->mother->uid;
                }
            }
        }
        
        // Add great great grandparents
        foreach ([$person->father, $person->mother] as $parent) {
            if ($parent) {
                if ($parent->father) {
                    if ($parent->father->father?->father) {
                        $ids[] = $parent->father->father->father->uid;
                    }
                    if ($parent->father->father?->mother) {
                        $ids[] = $parent->father->father->mother->uid;
                    }
                    if ($parent->father->mother?->father) {
                        $ids[] = $parent->father->mother->father->uid;
                    }
                    if ($parent->father->mother?->mother) {
                        $ids[] = $parent->father->mother->mother->uid;
                    }
                }
                if ($parent->mother) {
                    if ($parent->mother->father?->father) {
                        $ids[] = $parent->mother->father->father->uid;
                    }
                    if ($parent->mother->father?->mother) {
                        $ids[] = $parent->mother->father->mother->uid;
                    }
                    if ($parent->mother->mother?->father) {
                        $ids[] = $parent->mother->mother->father->uid;
                    }
                    if ($parent->mother->mother?->mother) {
                        $ids[] = $parent->mother->mother->mother->uid;
                    }
                }
            }
        }
        
        // Add grandchildren
        foreach ($person->getChildren() as $child) {
            foreach ($child->getChildren() as $grandchild) {
                $ids[] = $grandchild->uid;
            }
        }
        
        // Add great grandchildren
        foreach ($person->getChildren() as $child) {
            foreach ($child->getChildren() as $grandchild) {
                foreach ($grandchild->getChildren() as $greatGrandchild) {
                    $ids[] = $greatGrandchild->uid;
                }
            }
        }
        
        // Add great great grandchildren
        foreach ($person->getChildren() as $child) {
            foreach ($child->getChildren() as $grandchild) {
                foreach ($grandchild->getChildren() as $greatGrandchild) {
                    foreach ($greatGrandchild->getChildren() as $greatGreatGrandchild) {
                        $ids[] = $greatGreatGrandchild->uid;
                    }
                }
            }
        }

        return array_unique($ids);
    }

    private function formatUnifiedPerson(UnifiedPerson $p): array
    {
        return [
            'id' => $p->uid,
            'original_id' => $p->getOriginalId(),
            'first_name' => $p->first_name,
            'last_name' => $p->last_name,
            'full_name' => $p->full_name,
            'gender_id' => $p->gender_id,
            'gender_name' => $p->gender_name,
            'family_no' => $p->family_no,
            'member_no' => $p->member_no,
            'source' => $p->source,
            'is_member' => $p->isMember(),
            'is_external' => $p->isExternal(),
        ];
    }

    /**
     * Get available relationships for selection
     */
    public function getAvailableRelationships(): array
    {
        try {
            return Relationship::select('id', 'name')
                ->orderBy('name')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            // Fallback to hardcoded relationships if database query fails
            return [
                ['id' => 1, 'name' => 'Head'],
                ['id' => 2, 'name' => 'Father'],
                ['id' => 3, 'name' => 'Mother'],
                ['id' => 4, 'name' => 'Husband'],
                ['id' => 5, 'name' => 'Wife'],
                ['id' => 6, 'name' => 'Son'],
                ['id' => 7, 'name' => 'Daughter'],
                ['id' => 8, 'name' => 'Brother'],
                ['id' => 9, 'name' => 'Sister'],
                ['id' => 10, 'name' => 'Grand Father'],
                ['id' => 11, 'name' => 'Grand Mother'],
                ['id' => 12, 'name' => 'Grandson'],
                ['id' => 13, 'name' => 'Granddaughter'],
                ['id' => 14, 'name' => 'Great Grand Father'],
                ['id' => 15, 'name' => 'Great Grand Mother'],
                ['id' => 16, 'name' => 'Great Grandson'],
                ['id' => 17, 'name' => 'Great Granddaughter'],
                ['id' => 18, 'name' => 'Great Great Grand Father'],
                ['id' => 19, 'name' => 'Great Great Grand Mother'],
                ['id' => 20, 'name' => 'Great Great Grandson'],
                ['id' => 21, 'name' => 'Great Great Granddaughter'],
                ['id' => 22, 'name' => 'Uncle'],
                ['id' => 23, 'name' => 'Aunt'],
                ['id' => 24, 'name' => 'Nephew'],
                ['id' => 25, 'name' => 'Niece'],
                ['id' => 26, 'name' => 'Cousin Brother'],
                ['id' => 27, 'name' => 'Cousin Sister'],
                ['id' => 28, 'name' => 'Father-in-Law'],
                ['id' => 29, 'name' => 'Mother-in-Law'],
                ['id' => 30, 'name' => 'Son-in-Law'],
                ['id' => 31, 'name' => 'Daughter-in-Law'],
                ['id' => 32, 'name' => 'Brother-in-Law'],
                ['id' => 33, 'name' => 'Sister-in-Law'],
                ['id' => 34, 'name' => 'Grandfather-in-Law'],
                ['id' => 35, 'name' => 'Grandmother-in-Law'],
                ['id' => 36, 'name' => 'Great Grandfather-in-Law'],
                ['id' => 37, 'name' => 'Great Grandmother-in-Law'],
                ['id' => 38, 'name' => 'Grandson-in-Law'],
                ['id' => 39, 'name' => 'Granddaughter-in-Law'],
                ['id' => 40, 'name' => 'Great Grandson-in-Law'],
                ['id' => 41, 'name' => 'Great Granddaughter-in-Law'],
                ['id' => 42, 'name' => 'Great Great Grandson-in-Law'],
                ['id' => 43, 'name' => 'Great Great Granddaughter-in-Law'],
                ['id' => 44, 'name' => 'Great Nephew'],
                ['id' => 45, 'name' => 'Great Niece'],
                ['id' => 46, 'name' => 'Great Great Nephew'],
                ['id' => 47, 'name' => 'Great Great Niece'],
            ];
        }
    }
}
