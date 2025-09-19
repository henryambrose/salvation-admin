<?php

namespace Modules\Members\Services;

use Illuminate\Support\Facades\Log;
use Modules\Members\Models\Relationship;
use Modules\Members\Models\UnifiedPerson; // Add this import

class FamilyTreeService
{


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

        // ✅ Great Grandchild's spouse has higher priority than any sister/brother-in-law logic
        foreach ($p1->getChildren() as $child) {
            foreach ($child->getChildren() as $grandchild) {
                foreach ($grandchild->getChildren() as $greatGrandchild) {
                    if ($greatGrandchild->spouse_uid === $p2->uid || $p2->spouse_uid === $greatGrandchild->uid) {
                        return $p2->gender_id == 1 ? 'Great Grandson-in-Law' : 'Great Granddaughter-in-Law';
                    }
                }
            }
        }

        // NEW LOGIC: Handle males who married into the family
        if (
            $p1->gender_id == 1 && // Male
            $p1->spouse_uid && // Has spouse
            !$p1->father_uid && // No father (not born into family)
            !$p1->mother_uid
        ) { // No mother (not born into family)
            // Check if p2 is his spouse
            if ($p1->spouse_uid === $p2->uid) {
                return $p2->gender_id == 1 ? 'Husband' : 'Wife';
            }

            // Check if p2 is his spouse's grandparent (Grandfather-in-Law/Grandmother-in-Law)
            if ($p1->spouse) {
                // Check if p2 is his spouse's parent (Father-in-Law/Mother-in-Law)
                if ($p1->spouse->father_uid === $p2->uid) {
                    return 'Father-in-Law';
                }
                if ($p1->spouse->mother_uid === $p2->uid) {
                    return 'Mother-in-Law';
                }

                // Check if p2 is spouse's father's father (Grandfather-in-Law)
                if ($p1->spouse->father && $p1->spouse->father->father_uid === $p2->uid) {
                    return 'Grandfather-in-Law';
                }
                // Check if p2 is spouse's father's mother (Grandmother-in-Law)
                if ($p1->spouse->father && $p1->spouse->father->mother_uid === $p2->uid) {
                    return 'Grandmother-in-Law';
                }
                // Check if p2 is spouse's mother's father (Grandfather-in-Law)
                if ($p1->spouse->mother && $p1->spouse->mother->father_uid === $p2->uid) {
                    return 'Grandfather-in-Law';
                }
                // Check if p2 is spouse's mother's mother (Grandmother-in-Law)
                if ($p1->spouse->mother && $p1->spouse->mother->mother_uid === $p2->uid) {
                    return 'Grandmother-in-Law';
                }
                // Check if p2 is spouse's great grandparent (Great Grandfather-in-Law/Great Grandmother-in-Law)
                if ($p1->spouse->father && $p1->spouse->father->father) {
                    if ($p1->spouse->father->father->father_uid === $p2->uid) {
                        return 'Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->father->father->mother_uid === $p2->uid) {
                        return 'Great Grandmother-in-Law';
                    }
                }
                if ($p1->spouse->mother && $p1->spouse->mother->mother) {
                    if ($p1->spouse->mother->mother->father_uid === $p2->uid) {
                        return 'Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->mother->mother->mother_uid === $p2->uid) {
                        return 'Great Grandmother-in-Law';
                    }
                }
                if ($p1->spouse->father && $p1->spouse->father->father->father) {
                    if ($p1->spouse->father->father->father_uid === $p2->uid) {
                        return 'Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->father->father->mother_uid === $p2->uid) {
                        return 'Great Grandmother-in-Law';
                    }
                }
                if ($p1->spouse->mother && $p1->spouse->mother->mother?->mother) {
                    if ($p1->spouse->mother->mother->mother->father_uid === $p2->uid) {
                        return 'Great Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->mother->mother->mother->mother_uid === $p2->uid) {
                        return 'Great Great Grandmother-in-Law';
                    }
                }
                if ($p1->spouse->father && $p1->spouse->father->father?->father) {
                    if ($p1->spouse->father->father->father->father_uid === $p2->uid) {
                        return 'Great Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->father->father->father->mother_uid === $p2->uid) {
                        return 'Great Great Grandmother-in-Law';
                    }
                }
            }
            // Check if p2 is his spouse's sibling (Brother-in-Law/Sister-in-Law)
            if ($p1->spouse) {
                foreach ($p1->spouse->getSiblings() as $spouseSibling) {
                    if ($spouseSibling->uid === $p2->uid) {
                        return $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                    }
                }
            }

            // Check if p2 is his spouse's uncle/aunt (Uncle-in-Law/Aunt-in-Law)
            if ($p1->spouse) {

                if ($this->isUncleOrAunt($p1->spouse, $p2)) {
                    return $p2->gender_id == 1 ? 'Uncle-in-Law' : 'Aunt-in-Law';
                }
                if ($this->isSpouseOfUncleOrAunt($p1->spouse, $p2)) {
                    return $p2->gender_id == 1 ? 'Uncle-in-Law' : 'Aunt-in-Law';
                }
            }

            // NEW: Check if p2 is his spouse's cousin (Cousin-in-Law)
            if ($p1->spouse) {

                // Check cousins through spouse's father's side
                if ($p1->spouse->father) {
                    foreach ($p1->spouse->father->getSiblings() as $uncleAunt) {
                        foreach ($uncleAunt->getChildren() as $cousin) {
                            if ($cousin->uid === $p2->uid) {
                                return $p2->gender_id == 1 ? 'Cousin Brother-in-Law' : 'Cousin Sister-in-Law';
                            }
                        }
                    }
                }
                // Check cousins through spouse's mother's side
                if ($p1->spouse->mother) {
                    foreach ($p1->spouse->mother->getSiblings() as $uncleAunt) {
                        foreach ($uncleAunt->getChildren() as $cousin) {
                            if ($cousin->uid === $p2->uid) {
                                return $p2->gender_id == 1 ? 'Cousin Brother-in-Law' : 'Cousin Sister-in-Law';
                            }
                        }
                    }
                }
            }
        }
        // NEW LOGIC: Handle females who married into the family
        if (
            $p1->gender_id == 2 && // Female
            $p1->spouse_uid && // Has spouse
            !$p1->father_uid && // No father (not born into family)
            !$p1->mother_uid
        ) { // No mother (not born into family)



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

            // Check if p2 is her spouse's grandparent (Grandfather-in-Law/Grandmother-in-Law) - FIRST
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

            // Check if p2 is her spouse's great grandparent (Great Grandfather-in-Law/Great Grandmother-in-Law) - SECOND
            if ($p1->spouse) {
                if ($p1->spouse->father && $p1->spouse->father->father) {
                    if ($p1->spouse->father->father->father_uid === $p2->uid) {
                        return 'Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->father->father->mother_uid === $p2->uid) {
                        return 'Great Grandmother-in-Law';
                    }
                }
                if ($p1->spouse->mother && $p1->spouse->mother->father) {
                    if ($p1->spouse->mother->father->father_uid === $p2->uid) {
                        return 'Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->mother->father->mother_uid === $p2->uid) {
                        return 'Great Grandmother-in-Law';
                    }
                }
                if ($p1->spouse->mother && $p1->spouse->mother?->mother) {
                    if ($p1->spouse->mother->mother->father_uid === $p2->uid) {
                        return 'Great Grandfather-in-Law';
                    }
                    if ($p1->spouse->mother->mother->mother_uid === $p2->uid) {
                        return 'Great Grandmother-in-Law';
                    }
                }
            }

            // Check if p2 is her spouse's sibling (Brother-in-Law/Sister-in-Law) - SECOND
            if ($p1->spouse) {
                foreach ($p1->spouse->getSiblings() as $spouseSibling) {
                    if ($spouseSibling->uid === $p2->uid) {

                        return $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                    }
                }
            }

            // Check if p2 is his spouse's uncle/aunt (Uncle-in-Law/Aunt-in-Law)
            if ($p1->spouse) {
                // Check if p2 is his spouse's uncle/aunt (Uncle-in-Law/Aunt-in-Law)
                if ($p1->spouse) {

                    if ($this->isUncleOrAunt($p1->spouse, $p2)) {
                        return $p2->gender_id == 1 ? 'Uncle-in-Law' : 'Aunt-in-Law';
                    }
                    if ($this->isSpouseOfUncleOrAunt($p1->spouse, $p2)) {
                        return $p2->gender_id == 1 ? 'Uncle-in-Law' : 'Aunt-in-Law';
                    }
                }
            }

            // NEW: Check if p2 is his spouse's cousin (Cousin-in-Law)
            if ($p1->spouse) {

                // Check cousins through spouse's father's side
                if ($p1->spouse->father) {
                    foreach ($p1->spouse->father->getSiblings() as $uncleAunt) {
                        foreach ($uncleAunt->getChildren() as $cousin) {
                            if ($cousin->uid === $p2->uid) {
                                return $p2->gender_id == 1 ? 'Cousin Brother-in-Law' : 'Cousin Sister-in-Law';
                            }
                        }
                    }
                }
                // Check cousins through spouse's mother's side
                if ($p1->spouse->mother) {
                    foreach ($p1->spouse->mother->getSiblings() as $uncleAunt) {
                        foreach ($uncleAunt->getChildren() as $cousin) {
                            if ($cousin->uid === $p2->uid) {
                                return $p2->gender_id == 1 ? 'Cousin Brother-in-Law' : 'Cousin Sister-in-Law';
                            }
                        }
                    }
                }
            }
        }
        // ALSO CHECK: Handle when p2 is a male who married into the family
        if (
            $p2->gender_id == 1 && // Male
            $p2->spouse_uid && // Has spouse
            !$p2->father_uid && // No father (not born into family)
            !$p2->mother_uid
        ) { // No mother (not born into family)
            // Check if p1 is his spouse
            if ($p2->spouse_uid === $p1->uid) {
                return $p2->gender_id == 1 ? 'Husband' : 'Wife';
            }

            // Check if p1 is his spouse's parent (Father-in-Law/Mother-in-Law)
            if ($p2->spouse) {
                if ($p2->spouse->father_uid === $p1->uid) {
                    return 'Father-in-Law';
                }
                if ($p2->spouse->mother_uid === $p1->uid) {
                    return 'Mother-in-Law';
                }
            }

            // Check if p1 is his spouse's grandparent (Grandfather-in-Law/Grandmother-in-Law)
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

            // Check if p1 is his spouse's great grandparent (Great Grandfather-in-Law/Great Grandmother-in-Law)
            if ($p2->spouse) {
                if ($p2->spouse->father && $p2->spouse->father->father && $p2->spouse->father->father->father_uid === $p1->uid) {
                    return 'Great Grandfather-in-Law';
                }
                if ($p2->spouse->father && $p2->spouse->father->father && $p2->spouse->father->father->mother_uid === $p1->uid) {
                    return 'Great Grandmother-in-Law';
                }
                if ($p2->spouse->mother && $p2->spouse->mother->father && $p2->spouse->mother->father->father_uid === $p1->uid) {
                    return 'Great Grandfather-in-Law';
                }
                if ($p2->spouse->mother && $p2->spouse->mother->father && $p2->spouse->mother->father->mother_uid === $p1->uid) {
                    return 'Great Grandmother-in-Law';
                }
            }

            // Check if p1 is his spouse's great great grandparent (Great Great Grandfather-in-Law/Great Great Grandmother-in-Law)
            if ($p2->spouse) {
                if ($p2->spouse->father && $p2->spouse->father->father && $p2->spouse->father->father->father && $p2->spouse->father->father->father->father_uid === $p1->uid) {
                    return 'Great Great Grandfather-in-Law';
                }
                if ($p2->spouse->father && $p2->spouse->father->father && $p2->spouse->father->father->father && $p2->spouse->father->father->father->mother_uid === $p1->uid) {
                    return 'Great Great Grandmother-in-Law';
                }
                if ($p2->spouse->mother && $p2->spouse->mother->father && $p2->spouse->mother->father->father && $p2->spouse->mother->father->father->father_uid === $p1->uid) {
                    return 'Great Great Grandfather-in-Law';
                }
                if ($p2->spouse->mother && $p2->spouse->mother->father && $p2->spouse->mother->father->father && $p2->spouse->mother->father->father->mother_uid === $p1->uid) {
                    return 'Great Great Grandmother-in-Law';
                }
            }

            // Check if p1 is his spouse's sibling (Brother-in-Law/Sister-in-Law)
            if ($p2->spouse) {
                foreach ($p2->spouse->getSiblings() as $spouseSibling) {
                    if ($spouseSibling->uid === $p1->uid) {
                        return $p1->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                    }
                }
            }

            // Check if p1 is his spouse's uncle/aunt (Uncle-in-Law/Aunt-in-Law)
            if ($p2->spouse) {
                // Check if p2 is his spouse's uncle/aunt (Uncle-in-Law/Aunt-in-Law)
                if ($p2->spouse) {

                    if ($this->isUncleOrAunt($p2->spouse, $p1)) {
                        return $p1->gender_id == 1 ? 'Uncle-in-Law' : 'Aunt-in-Law';
                    }

                    if ($p1->spouse && $this->isSpouseOfUncleOrAunt($p1->spouse, $p2)) {
                        return $p1->gender_id == 1 ? 'Uncle-in-Law' : 'Aunt-in-Law';
                    }
                }
            }

            // NEW: Check if p1 is his spouse's cousin (Cousin-in-Law)
            if ($p2->spouse) {
                // Check cousins through spouse's father's side
                if ($p2->spouse->father) {
                    foreach ($p2->spouse->father->getSiblings() as $uncleAunt) {
                        foreach ($uncleAunt->getChildren() as $cousin) {
                            if ($cousin->uid === $p1->uid) {
                                return $p2->gender_id == 1 ? 'Cousin Brother-in-Law' : 'Cousin Sister-in-Law';
                            }
                        }
                    }
                }
                // Check cousins through spouse's mother's side
                if ($p2->spouse->mother) {
                    foreach ($p2->spouse->mother->getSiblings() as $uncleAunt) {
                        foreach ($uncleAunt->getChildren() as $cousin) {
                            if ($cousin->uid === $p1->uid) {
                                return $p1->gender_id == 1 ? 'Cousin Brother-in-Law' : 'Cousin Sister-in-Law';
                            }
                        }
                    }
                }
            }
        }
        // MOVED: Handle when p2 is a female who married into the family (moved AFTER male logic)
        if (
            $p2->gender_id == 2 && // Female
            $p2->spouse_uid && // Has spouse
            !$p2->father_uid && // No father (not born into family)
            !$p2->mother_uid
        ) { // No mother (not born into family)
            // Check if p1 is her spouse
            if ($p2->spouse_uid === $p1->uid) {
                return $p2->gender_id == 1 ? 'Husband' : 'Wife';
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
                foreach ($p2->spouse->getSiblings() as $spouseSibling) {
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
                // Check if p1 is his spouse's uncle/aunt (Uncle-in-Law/Aunt-in-Law)
                if ($p2->spouse) {
                    if ($p2->spouse->father) {
                        foreach ($p2->spouse->father->getSiblings() as $uncleAunt) {
                            if ($uncleAunt->uid === $p1->uid) {
                                return $p1->gender_id == 1 ? 'Uncle-in-Law' : 'Aunt-in-Law';
                            }
                        }
                    }
                    if ($p2->spouse->mother) {
                        foreach ($p2->spouse->mother->getSiblings() as $uncleAunt) {
                            if ($uncleAunt->uid === $p1->uid) {
                                return $p1->gender_id == 1 ? 'Uncle-in-Law' : 'Aunt-in-Law';
                            }
                        }
                    }
                }
            }
            // NEW: Check if p1 is his spouse's cousin (Cousin-in-Law)
            if ($p2->spouse) {
                // Check cousins through spouse's father's side
                if ($p2->spouse->father) {
                    foreach ($p2->spouse->father->getSiblings() as $uncleAunt) {
                        foreach ($uncleAunt->getChildren() as $cousin) {
                            if ($cousin->uid === $p1->uid) {
                                return $p2->gender_id == 1 ? 'Cousin Brother-in-Law' : 'Cousin Sister-in-Law';
                            }
                        }
                    }
                }
                // Check cousins through spouse's mother's side
                if ($p2->spouse->mother) {
                    foreach ($p2->spouse->mother->getSiblings() as $uncleAunt) {
                        foreach ($uncleAunt->getChildren() as $cousin) {
                            if ($cousin->uid === $p1->uid) {
                                return $p2->gender_id == 1 ? 'Cousin Brother-in-Law' : 'Cousin Sister-in-Law';
                            }
                        }
                    }
                }
            }
        }

        // Direct parent/child - These should come FIRST
        if ($p1->father_uid === $p2->uid) {
            return 'Father';
        }
        if ($p1->mother_uid === $p2->uid) {
            return 'Mother';
        }
        if ($p2->father_uid === $p1->uid || $p2->mother_uid === $p1->uid) {
            $relationship = $p2->gender_id == 1 ? 'Son' : 'Daughter';
            return $relationship;
        }

        // Spouse
        if ($p1->spouse_uid === $p2->uid) {
            $relationship = $p2->gender_id == 1 ? 'Husband' : 'Wife';
            return $relationship;
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

        // Nephew / Niece
        if ($this->isNephewOrNiece($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Nephew' : 'Niece';
        }
        // Cousin - Move this AFTER grandparent checks
        if ($this->isCousin($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Cousin Brother' : 'Cousin Sister';
        }

        // Sibling's spouse (sister/brother-in-law) - Check this BEFORE spouse's parent
        foreach ($p1->getSiblings() as $sibling) {
            if ($sibling->spouse_uid === $p2->uid) {
                $relationship = $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                return $relationship;
            }
        }

        // Spouse's parent → Move this BEFORE sibling's spouse to avoid conflicts
        if ($p1->spouse) {
            if ($p1->spouse->father_uid === $p2->uid) {
                return 'Father-in-Law';
            }
            if ($p1->spouse->mother_uid === $p2->uid) {
                return 'Mother-in-Law';
            }
        }
        // Spouse's sibling (sister/brother-in-law) - This should come after spouse's parent
        if ($p1->spouse) {
            foreach ($p1->spouse->getSiblings() as $sibling) {
                if ($sibling->uid === $p2->uid) {
                    $relationship = $p2->gender_id == 1 ? 'Brother-in-Law' : 'Sister-in-Law';
                    return $relationship;
                }
            }
        }
        if ($p2->spouse) {
            // Check if p2 is his spouse's uncle/aunt (Uncle-in-Law/Aunt-in-Law)
            if ($p2->spouse) {

                if ($this->isUncleOrAunt($p2->spouse, $p1)) {
                    return $p1->gender_id == 1 ? 'Uncle-in-Law' : 'Aunt-in-Law';
                }
                if ($p1->spouse && $this->isSpouseOfUncleOrAunt($p1->spouse, $p2)) {
                    return $p1->gender_id == 1 ? 'Uncle-in-Law' : 'Aunt-in-Law';
                }
            }
        }
        $inLaw = $this->checkInLawRelationship($p1, $p2);
        if ($inLaw) {
            return $inLaw;
        }

        return 'Family Member';
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

    private function areSiblings(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        return $p1->family_no === $p2->family_no && (
            (! empty($p1->father_uid) && $p1->father_uid === $p2->father_uid) ||
            (! empty($p1->mother_uid) && $p1->mother_uid === $p2->mother_uid)
        );
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

    private function isUncleOrAunt(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        return ($p1->father && $this->areSiblings($p1->father, $p2)) ||
            ($p1->mother && $this->areSiblings($p1->mother, $p2));
    }

    private function isSpouseOfUncleOrAunt(UnifiedPerson $p1, UnifiedPerson $p2): bool
    {
        // Get P1's uncles and aunts
        $unclesAndAunts = [];

        // Check father's siblings
        if ($p1->father) {
            foreach ($p1->father->getSiblings() as $sibling) {
                $unclesAndAunts[] = $sibling;
            }
        }

        // Check mother's siblings
        if ($p1->mother) {
            foreach ($p1->mother->getSiblings() as $sibling) {
                $unclesAndAunts[] = $sibling;
            }
        }

        // Check if P2 is the spouse of any of these uncles/aunts
        foreach ($unclesAndAunts as $uncleAunt) {
            if ($uncleAunt->spouse_uid === $p2->uid) {
                return true;
            }
        }

        return false;
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

        //Great Grandson-in-Law / Great Granddaughter-in-Law: if p2 is spouse of p1's great grandchild
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

        // Check if P2 is P1's great grandparent (Great Grandfather/Great Grandmother)
        if ($p1->father) {
            if ($p1->father->father && $p1->father->father->uid === $p2->uid) {
                return $p2->gender_id == 1 ? 'Great Grandfather' : 'Great Grandmother';
            }
            if ($p1->father->mother && $p1->father->mother->uid === $p2->uid) {
                return $p2->gender_id == 1 ? 'Great Grandfather' : 'Great Grandmother';
            }
        }
        if ($p1->mother) {
            if ($p1->mother->father && $p1->mother->father->uid === $p2->uid) {
                return $p2->gender_id == 1 ? 'Great Grandfather' : 'Great Grandmother';
            }
            if ($p1->mother->mother && $p1->mother->mother->uid === $p2->uid) {
                return $p2->gender_id == 1 ? 'Great Grandfather' : 'Great Grandmother';
            }
        }



        // Check if P2 is P1's uncle/aunt (biological)
        if ($this->isUncleOrAunt($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Uncle' : 'Aunt';
        }
        // Check if P2 is spouse of P1's uncle/aunt (in-law)
        if ($this->isSpouseOfUncleOrAunt($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Uncle' : 'Aunt';
        }

        // Check if P2 is P1's sibling
        if ($this->areSiblings($p1, $p2)) {
            return $p2->gender_id == 1 ? 'Brother' : 'Sister';
        }

        // Check if P2 is P1's cousin
        if ($this->isCousin($p1, $p2)) {
            return 'Cousin';
        }

        // Extended in-law relationships (moved to end to avoid interference with direct family relationships)
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

        // Extended in-law: sibling's children's spouses (Nephew-in-Law/Niece-in-Law)
        foreach ($p1->getSiblings() as $sibling) {
            foreach ($sibling->getChildren() as $siblingChild) {
                if ($siblingChild->spouse_uid === $p2->uid) {
                    return $p2->gender_id == 1 ? 'Nephew-in-Law' : 'Niece-in-Law';
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
