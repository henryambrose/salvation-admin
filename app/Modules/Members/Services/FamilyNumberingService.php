<?php

namespace Modules\Members\Services;

use Modules\Members\Models\Member;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FamilyNumberingService
{
    protected $churchCode;

    public function __construct($churchCode = null)
    {
        $this->churchCode = strtoupper($churchCode ?? config('app.church_code', 'SAL'));
    }

    /**
     * Generate family group number: SAL-XXX
     */
    public function generateFamilyGroupNumber($churchCode = null)
    {
        try {
            $churchCode = $churchCode ?? $this->churchCode;
            $churchCode = strtoupper($churchCode);

            // Find the highest family group number for this church
            $highestFamily = DB::table('members')
                ->whereNotNull('family_no')
                ->where('family_no', 'like', $churchCode . '-%')
                ->orderByRaw('CAST(SUBSTRING_INDEX(family_no, "-", -1) AS UNSIGNED) DESC')
                ->first();

            if ($highestFamily) {
                // Parse the highest family number to get the group sequence
                $parsed = $this->parseFamilyNumber($highestFamily->family_no);
                $nextSequence = $parsed['family_group'] + 1;
            } else {
                // No existing families for this church, start with 1
                $nextSequence = 1;
            }

            // Format: SAL-XXX (3 digits)
            $familyGroupSequence = str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
            $result = "{$churchCode}-{$familyGroupSequence}";
            return $result;
        } catch (\Exception $e) {
            Log::error('Error generating family group number: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Generate member number within a family group: SAL-XXX
     * Note: This method now returns just the family group number since all members share the same family number
     */
    public function generateMemberNumberInFamily($familyGroup, $churchCode = null)
    {
        // For new family numbering system, all members in a family share the same family number
        // The family group is the family number itself
        return $familyGroup;
    }

    /**
     * Generate independent member number: YYYY-SAL-MNNNNN
     * Note: The YYYY prefix reflects registration year, but the sequence
     * (NNNNN) continues incrementing across all years for the church.
     * This ensures globally unique member IDs while preserving year context.
     */
    public function generateMemberNumber($year = null, $churchCode = null)
    {
        try {
            $year = $year ?? date('Y');
            $churchCode = $churchCode ?? $this->churchCode;
            $churchCode = strtoupper($churchCode);

            // Find the highest member sequence across all years for this church
            $highestMember = DB::table('members')
                ->where('member_no', 'like', '%' . $churchCode . '-M%')
                ->whereNotNull('member_no')
                ->orderByRaw('CAST(REPLACE(SUBSTRING_INDEX(member_no, "-", -1), "M", "") AS UNSIGNED) DESC')
                ->first();

            if ($highestMember) {
                // Parse the highest member number to get the sequence
                $parsed = $this->parseMemberNumber($highestMember->member_no);
                $nextSequence = $parsed['member_sequence'] + 1;
            } else {
                // No existing members for this church, start with 1
                $nextSequence = 1;
            }

            // Format: YYYY-SAL-MNNNNN
            $memberSequence = str_pad($nextSequence, 6, '0', STR_PAD_LEFT);
            $result = "{$year}-{$churchCode}-M{$memberSequence}";

            return $result;
        } catch (\Exception $e) {
            Log::error('Error generating member number: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Parse family number: SAL-XXX
     */
    public function parseFamilyNumber($familyNo)
    {
        // Parse: SAL-XXX (3 digits) or SAL-XXXX (4 digits)
        if (! preg_match('/^([A-Z]{3})-(\d{3,4})$/', $familyNo, $matches)) {
            throw new \InvalidArgumentException('Invalid family number format. Expected: ' . $this->churchCode . '-XXX or ' . $this->churchCode . '-XXXX');
        }

        return [
            'church_code' => $matches[1],
            'family_group' => (int) $matches[2],
            'member_sequence' => 1, // All members in a family share the same family number
        ];
    }

    /**
     * Parse member number: YYYY-SAL-MNNNNN
     */
    public function parseMemberNumber($memberNo)
    {
        // Parse: YYYY-SAL-MNNNNN
        if (! preg_match('/^(\d{4})-([A-Z]{3})-M(\d{6})$/', $memberNo, $matches)) {
            throw new \InvalidArgumentException('Invalid member number format. Expected: YYYY-SAL-MNNNNN');
        }

        return [
            'year' => $matches[1],
            'church_code' => $matches[2],
            'member_sequence' => (int) $matches[3],
        ];
    }

    public function validateFamilyNumber($familyNo)
    {
        try {
            $this->parseFamilyNumber($familyNo);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function validateMemberNumber($memberNo)
    {
        try {
            $this->parseMemberNumber($memberNo);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFamilyInfo($familyNo)
    {
        $parsed = $this->parseFamilyNumber($familyNo);

        return [
            'church_code' => $parsed['church_code'],
            'family_group' => $parsed['family_group'],
            'member_sequence' => $parsed['member_sequence'],
            'year' => date('Y'), // Current year for new members
            'church_name' => $this->churchCode,
        ];
    }

    public function getMemberInfo($memberNo)
    {
        $parsed = $this->parseMemberNumber($memberNo);

        return [
            'year' => $parsed['year'],
            'church_code' => $parsed['church_code'],
            'member_sequence' => $parsed['member_sequence'],
            'church_name' => $this->churchCode,
        ];
    }



    public function handleFamilyMove($familyNo, $newCommunityId, $newChurchCode = null)
    {
        // Family number stays the same, only community and church code change
        $members = Member::where('family_no', $this->getFamilyGroupFromNumber($familyNo))->get();

        foreach ($members as $member) {
            $updateData = ['community_id' => $newCommunityId];

            // Note: church_code is not stored in database, it's from .env config
            // If church code changes, family numbers would need to be regenerated

            $member->update($updateData);
        }

        return $members;
    }

    public function handleMarriage($brideId, $groomId, $brideJoinsGroom = true)
    {
        $bride = Member::findOrFail($brideId);
        $groom = Member::findOrFail($groomId);

        // Import ExternalMember model
        $externalMemberClass = \Modules\Members\Models\ExternalMember::class;

        DB::transaction(function () use ($bride, $groom, $brideJoinsGroom, $externalMemberClass) {
            if ($brideJoinsGroom) {
                // Traditional: Bride joins groom's family
                $this->createExternalMemberRecord($bride, $bride->family_no, $externalMemberClass);

                // Update bride to join groom's family
                $bride->update([
                    'birth_family_no' => $bride->family_no,  // Save birth family
                    'family_no' => $groom->family_no,        // Join groom's family
                    'father_id' => null,                     // Clear parent links
                    'mother_id' => null,                     // Clear parent links
                    'spouse_id' => $groom->id,               // Link to groom
                    'marital_status' => 'Married'
                ]);

                // Update groom
                $groom->update([
                    'spouse_id' => $bride->id,
                    'marital_status' => 'Married'
                ]);
            } else {
                // Rare case: Groom joins bride's family
                $this->createExternalMemberRecord($groom, $groom->family_no, $externalMemberClass);

                // Update groom to join bride's family
                $groom->update([
                    'birth_family_no' => $groom->family_no,  // Save birth family
                    'family_no' => $bride->family_no,        // Join bride's family
                    'father_id' => null,                     // Clear parent links
                    'mother_id' => null,                     // Clear parent links
                    'spouse_id' => $bride->id,               // Link to bride
                    'marital_status' => 'Married'
                ]);

                // Update bride
                $bride->update([
                    'spouse_id' => $groom->id,
                    'marital_status' => 'Married'
                ]);
            }
        });

        return [
            'bride' => $bride->fresh(),
            'groom' => $groom->fresh(),
        ];
    }

    /**
     * Create external member record in birth family
     */
    private function createExternalMemberRecord($member, $birthFamilyNo, $externalMemberClass)
    {
        $externalMemberClass::create([
            'family_no' => $birthFamilyNo,
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'father_id' => $member->father_id,           // Preserve parent links
            'mother_id' => $member->mother_id,           // Preserve parent links
            'spouse_id' => null,                         // Will be set after marriage
            'relationship_id' => $member->relationship_id,
            'gender_id' => $member->gender_id,
            'community_id' => $member->community_id,
            'father_source' => $member->father_source ?? 'Member',
            'mother_source' => $member->mother_source ?? 'Member',
            'spouse_source' => 'Member',
        ]);
    }

    public function getFamilyGroupFromNumber($familyNo)
    {
        $parsed = $this->parseFamilyNumber($familyNo);

        return $parsed['church_code'] . '-' . str_pad($parsed['family_group'], 3, '0', STR_PAD_LEFT);
    }

    public function getEffectiveFamilyNumber($member)
    {
        // Return current family number if set, otherwise birth family number
        return $member->current_family_no ?: $member->family_no;
    }

    public function getFamilyMembers($familyGroup, $includeMarriedMembers = true)
    {
        $query = Member::where('family_no', $familyGroup);

        if (! $includeMarriedMembers) {
            $query->where('marital_status', '!=', 'Married');
        }

        return $query->with(['relationship', 'community'])
            ->orderBy('member_sequence')
            ->get();
    }

    public function getChurchStatistics($churchCode = null, $year = null)
    {
        $churchCode = $churchCode ?? $this->churchCode;

        $query = Member::where('family_no', 'like', $churchCode . '-%');

        if ($year) {
            $query->where('registration_year', $year);
        }

        $totalMembers = $query->count();
        $totalFamilies = $query->distinct('family_no')->count('family_no');

        return [
            'church_code' => $churchCode,
            'year' => $year,
            'total_members' => $totalMembers,
            'total_families' => $totalFamilies,
            'average_members_per_family' => $totalFamilies > 0 ? round($totalMembers / $totalFamilies, 2) : 0,
        ];
    }

    public function searchFamilies($query)
    {
        // First, find families that match the query
        $familyNumbers = Member::where('family_no', 'like', "%{$query}%")
            ->orWhere('member_no', 'like', "%{$query}%")
            ->orWhere('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
            ->distinct()
            ->pluck('family_no')
            ->filter()
            ->take(10);

        // Then get family groups with member information
        $families = [];
        foreach ($familyNumbers as $familyNo) {
            $familyGroup = $this->getFamilyGroupFromNumber($familyNo);
            $members = Member::where('family_no', $familyGroup)
                ->with(['relationship', 'community'])
                ->orderBy('member_sequence')
                ->get();

            if ($members->count() > 0) {
                $families[] = [
                    'family_no' => $familyNo, // Use the actual family number from database
                    'family_group' => $familyGroup, // Keep family group for reference
                    'member_count' => $members->count(),
                    'members' => $members->map(function ($member) {
                        return $member->first_name . ' ' . $member->last_name;
                    })->toArray(),
                    'community' => $members->first()->community?->name,
                    'registration_year' => $members->first()->registration_year,
                ];
            }
        }

        return $families;
    }
}
