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
            'grandchildren' => $this->getGrandchildren($person),
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

    public function calculateRelationship(UnifiedPerson $p1, UnifiedPerson $p2): string
    {
        if ($p1->family_no !== $p2->family_no) {
            return 'Family Member';
        }

        // Direct parent/child
        if ($p1->father_uid === $p2->uid) {
            return 'Father';
        }
        if ($p1->mother_uid === $p2->uid) {
            return 'Mother';
        }
        if ($p2->father_uid === $p1->uid || $p2->mother_uid === $p1->uid) {
            return $p2->gender_id == 1 ? 'Son' : 'Daughter';
        }

        // Spouse
        if ($p1->spouse_uid === $p2->uid) {
            return $p2->gender_id == 1 ? 'Husband' : 'Wife';
        }
        // ✅ Child’s spouse has higher priority than any sister/brother-in-law logic
        foreach ($p1->getChildren() as $child) {
            if ($child->spouse_uid === $p2->uid || $p2->spouse_uid === $child->uid) {
                return $p2->gender_id == 1 ? 'Son-in-Law' : 'Daughter-in-Law';
            }
        }
        // Spouse’s parent → 🔼 move this up before siblings
        if ($p1->spouse) {
            if ($p1->spouse->father_uid === $p2->uid) {
                return 'Father-in-Law';
            }
            if ($p1->spouse->mother_uid === $p2->uid) {
                return 'Mother-in-Law';
            }
        }

        // Sibling
        if ($this->areSiblings($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Brother' : 'Sister';
        }

        // Sibling’s spouse (sister/brother-in-law)
        foreach ($p1->getSiblings() as $sibling) {
            if ($sibling->spouse_uid === $p2->uid) {
                return $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
            }
        }

        // Spouse’s sibling (sister/brother-in-law)
        if ($p1->spouse) {
            foreach ($p1->spouse->getSiblings() as $sibling) {
                if ($sibling->uid === $p2->uid) {
                    return $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                }
            }
        }

        // Grandparent
        if ($this->isGrandparent($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Grand Father' : 'Grand Mother';
        }

        // Grandchild
        if ($this->isGrandchild($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Grandson' : 'Granddaughter';
        }

        // Uncle / Aunt
        if ($this->isUncleOrAunt($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Uncle' : 'Aunt';
        }

        // Nephew / Niece
        if ($this->isNephewOrNiece($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Nephew' : 'Niece';
        }

        // Cousin
        if ($this->isCousin($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Cousin Brother' : 'Cousin Sister';
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

    private function isGrandchild(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        return $this->isGrandparent($p2, $p1);
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
        // Parent-in-law: if p2 is parent of p1's spouse
        if ($p1->spouse) {
            if ($p1->spouse->father_uid === $p2->uid) {
                return 'Father-in-Law';
            }
            if ($p1->spouse->mother_uid === $p2->uid) {
                return 'Mother-in-Law';
            }

            // Siblings of spouse → Brother-in-law / Sister-in-law
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

        return null;
    }

    private function getImmediateFamilyIds(UnifiedPerson $person): array
    {
        $ids = array_filter([
            $person->father_uid,
            $person->mother_uid,
            $person->spouse_uid,
        ]);

        foreach ($person->getChildren() as $child) {
            $ids[] = $child->uid;
        }
        foreach ($person->getSiblings() as $sibling) {
            $ids[] = $sibling->uid;
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
                ['id' => 14, 'name' => 'Uncle'],
                ['id' => 15, 'name' => 'Aunt'],
                ['id' => 16, 'name' => 'Nephew'],
                ['id' => 17, 'name' => 'Niece'],
                ['id' => 18, 'name' => 'Cousin Brother'],
                ['id' => 19, 'name' => 'Cousin Sister'],
                ['id' => 20, 'name' => 'Father-in-Law'],
                ['id' => 21, 'name' => 'Mother-in-Law'],
                ['id' => 22, 'name' => 'Son-in-Law'],
                ['id' => 23, 'name' => 'Daughter-in-Law'],
                ['id' => 24, 'name' => 'Brother-in-Law'],
                ['id' => 25, 'name' => 'Sister-in-Law'],
            ];
        }
    }
}
