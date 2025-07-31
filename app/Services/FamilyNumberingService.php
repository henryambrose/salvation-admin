<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Facades\DB;

class FamilyNumberingService
{
    protected $churchCode;

    public function __construct($churchCode = 'SAL')
    {
        $this->churchCode = strtoupper($churchCode);
    }

    /**
     * Generate family group number: SAL-XXX
     */
    public function generateFamilyGroupNumber($churchCode = null)
    {
        $churchCode = $churchCode ?? $this->churchCode;
        $churchCode = strtoupper($churchCode);
        
        // Find the highest family group number for this church
        $highestFamily = DB::table('members')
            ->where('church_code', $churchCode)
            ->whereNotNull('family_no')
            ->orderByRaw('CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(family_no, "-", 2), "-", -1) AS UNSIGNED) DESC')
            ->first();
        
        if ($highestFamily) {
            // Parse the highest family number to get the group sequence
            $parsed = $this->parseFamilyNumber($highestFamily->family_no);
            $nextSequence = $parsed['family_group'] + 1;
        } else {
            // No existing families for this church, start with 1
            $nextSequence = 1;
        }
        
        // Format: SAL-XXX
        $familyGroupSequence = str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
        
        return "{$churchCode}-{$familyGroupSequence}";
    }

    /**
     * Generate member number within a family group: SAL-XXX-YYY
     */
    public function generateMemberNumberInFamily($familyGroup, $churchCode = null)
    {
        $churchCode = $churchCode ?? $this->churchCode;
        $churchCode = strtoupper($churchCode);
        
        // Get next member sequence for this family group
        $lastMember = Member::where('family_no', 'like', "{$familyGroup}-%")
            ->orderBy('member_sequence', 'desc')
            ->first();
        
        $memberSequence = $lastMember ? $lastMember->member_sequence + 1 : 1;
        
        // Format: SAL-XXX-YYY
        $memberPart = str_pad($memberSequence, 3, '0', STR_PAD_LEFT);
        
        return "{$familyGroup}-{$memberPart}";
    }

    /**
     * Generate independent member number: YYYY-SAL-MNNNNN
     */
    public function generateMemberNumber($year = null, $churchCode = null)
    {
        $year = $year ?? date('Y');
        $churchCode = $churchCode ?? $this->churchCode;
        $churchCode = strtoupper($churchCode);
        
        // Find the highest member sequence for this year and church
        $highestMember = DB::table('members')
            ->where('registration_year', $year)
            ->where('church_code', $churchCode)
            ->whereNotNull('member_no')
            ->orderByRaw('CAST(REPLACE(SUBSTRING_INDEX(member_no, "-", -1), "M", "") AS UNSIGNED) DESC')
            ->first();
        
        if ($highestMember) {
            // Parse the highest member number to get the sequence
            $parsed = $this->parseMemberNumber($highestMember->member_no);
            $nextSequence = $parsed['member_sequence'] + 1;
        } else {
            // No existing members for this year/church, start with 1
            $nextSequence = 1;
        }
        
        // Format: YYYY-SAL-MNNNNN
        $memberSequence = str_pad($nextSequence, 6, '0', STR_PAD_LEFT);
        
        return "{$year}-{$churchCode}-M{$memberSequence}";
    }

    /**
     * Parse family number: SAL-XXX-YYY
     */
    public function parseFamilyNumber($familyNo)
    {
        // Parse: SAL-XXX-YYY
        if (!preg_match('/^([A-Z]{3})-(\d{3})-(\d{3})$/', $familyNo, $matches)) {
            throw new \InvalidArgumentException('Invalid family number format. Expected: SAL-XXX-YYY');
        }
        
        return [
            'church_code' => $matches[1],
            'family_group' => (int) $matches[2],
            'member_sequence' => (int) $matches[3]
        ];
    }

    /**
     * Parse member number: YYYY-SAL-MNNNNN
     */
    public function parseMemberNumber($memberNo)
    {
        // Parse: YYYY-SAL-MNNNNN
        if (!preg_match('/^(\d{4})-([A-Z]{3})-M(\d{6})$/', $memberNo, $matches)) {
            throw new \InvalidArgumentException('Invalid member number format. Expected: YYYY-SAL-MNNNNN');
        }
        
        return [
            'year' => $matches[1],
            'church_code' => $matches[2],
            'member_sequence' => (int) $matches[3]
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
            'church_name' => $this->getChurchName($parsed['church_code'])
        ];
    }

    public function getMemberInfo($memberNo)
    {
        $parsed = $this->parseMemberNumber($memberNo);
        
        return [
            'year' => $parsed['year'],
            'church_code' => $parsed['church_code'],
            'member_sequence' => $parsed['member_sequence'],
            'church_name' => $this->getChurchName($parsed['church_code'])
        ];
    }

    private function getChurchName($churchCode)
    {
        // You can extend this to fetch from a churches table
        $churchNames = [
            'SAL' => 'Salvation Church',
            'ABC' => 'Another Church',
            'XYZ' => 'Example Church'
        ];
        
        return $churchNames[$churchCode] ?? 'Unknown Church';
    }

    public function handleFamilyMove($familyNo, $newCommunityId, $newChurchCode = null)
    {
        // Family number stays the same, only community and church code change
        $members = Member::where('family_no', 'like', $this->getFamilyGroupFromNumber($familyNo) . '-%')->get();
        
        foreach ($members as $member) {
            $updateData = ['community_id' => $newCommunityId];
            
            if ($newChurchCode) {
                $updateData['church_code'] = strtoupper($newChurchCode);
            }
            
            $member->update($updateData);
        }
        
        return $members;
    }

    public function handleMarriage($memberId, $spouseId)
    {
        $member = Member::findOrFail($memberId);
        $spouse = Member::findOrFail($spouseId);
        
        // Determine which family group to use (use spouse's family group)
        $spouseFamilyGroup = $this->getFamilyGroupFromNumber($spouse->family_no);
        
        // Generate new member number for the marrying member in spouse's family
        $newMemberNumber = $this->generateMemberNumberInFamily($spouseFamilyGroup);
        
        // Update marital status
        $member->update([
            'marital_status' => 'married', 
            'spouse_member_id' => $spouseId,
            'family_no' => $newMemberNumber
        ]);
        
        $spouse->update([
            'marital_status' => 'married', 
            'spouse_member_id' => $memberId
        ]);
        
        return [
            'member' => $member->fresh(),
            'spouse' => $spouse->fresh()
        ];
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
        $query = Member::where('family_no', 'like', $familyGroup . '-%');
        
        if (!$includeMarriedMembers) {
            $query->where('marital_status', '!=', 'married');
        }
        
        return $query->with(['relationship', 'community'])
            ->orderBy('member_sequence')
            ->get();
    }

    public function getChurchStatistics($churchCode = null, $year = null)
    {
        $churchCode = $churchCode ?? $this->churchCode;
        
        $query = Member::where('church_code', $churchCode);
        
        if ($year) {
            $query->where('registration_year', $year);
        }
        
        $totalMembers = $query->count();
        $totalFamilies = $query->distinct('family_no')->count();
        
        return [
            'church_code' => $churchCode,
            'year' => $year,
            'total_members' => $totalMembers,
            'total_families' => $totalFamilies,
            'average_members_per_family' => $totalFamilies > 0 ? round($totalMembers / $totalFamilies, 2) : 0
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
            $members = Member::where('family_no', 'like', $familyGroup . '-%')
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
                    'registration_year' => $members->first()->registration_year
                ];
            }
        }
        return $families;
    }
} 