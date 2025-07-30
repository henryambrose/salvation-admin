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
    
    public function generateFamilyNumber($year = null, $churchCode = null)
    {
        $year = $year ?? date('Y');
        $churchCode = $churchCode ?? $this->churchCode;
        $churchCode = strtoupper($churchCode);
        
        // Get or create sequence for this year and church
        $sequence = DB::table('church_family_numbering_sequences')
            ->where('year', $year)
            ->where('church_code', $churchCode)
            ->lockForUpdate()
            ->first();
        
        if (!$sequence) {
            // Create new sequence for this year and church
            $nextSequence = 1;
            DB::table('church_family_numbering_sequences')->insert([
                'year' => $year,
                'church_code' => $churchCode,
                'last_sequence' => $nextSequence,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            // Increment existing sequence
            $nextSequence = $sequence->last_sequence + 1;
            DB::table('church_family_numbering_sequences')
                ->where('id', $sequence->id)
                ->update([
                    'last_sequence' => $nextSequence,
                    'updated_at' => now()
                ]);
        }
        
        // Format: YYYY-SAL-FNNNNN
        $familySequence = str_pad($nextSequence, 6, '0', STR_PAD_LEFT);
        
        return "{$year}-{$churchCode}-{$familySequence}";
    }
    
    public function generateMemberNumber($familyNo, $familySequence)
    {
        // Get next member sequence for this family
        $lastMember = Member::where('family_no', $familyNo)
            ->orderBy('member_sequence', 'desc')
            ->first();
        
        $memberSequence = $lastMember ? $lastMember->member_sequence + 1 : 1;
        
        // Format: FNNNNN-MNNN (Family sequence + Member sequence)
        $familyPart = str_pad($familySequence, 6, '0', STR_PAD_LEFT);
        $memberPart = str_pad($memberSequence, 3, '0', STR_PAD_LEFT);
        
        return "{$familyPart}-{$memberPart}";
    }
    
    public function parseFamilyNumber($familyNo)
    {
        // Parse: YYYY-SAL-FNNNNN
        if (!preg_match('/^(\d{4})-([A-Z]{3})-(\d{6})$/', $familyNo, $matches)) {
            throw new \InvalidArgumentException('Invalid family number format. Expected: YYYY-SAL-FNNNNN');
        }
        
        return [
            'year' => $matches[1],
            'church_code' => $matches[2],
            'family_sequence' => (int) $matches[3]
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
    
    public function getFamilyInfo($familyNo)
    {
        $parsed = $this->parseFamilyNumber($familyNo);
        
        return [
            'year' => $parsed['year'],
            'church_code' => $parsed['church_code'],
            'family_sequence' => $parsed['family_sequence'],
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
        $members = Member::where('family_no', $familyNo)->get();
        
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
        
        // Update member's current family to spouse's family
        $member->update([
            'current_family_no' => $spouse->family_no,
            'marital_status' => 'married',
            'spouse_member_id' => $spouseId,
            'marriage_date' => now()
        ]);
        
        // Update spouse's current family to member's family (if different)
        if ($spouse->family_no !== $member->family_no) {
            $spouse->update([
                'current_family_no' => $member->family_no,
                'marital_status' => 'married',
                'spouse_member_id' => $memberId,
                'marriage_date' => now()
            ]);
        }
        
        return [
            'member' => $member->fresh(),
            'spouse' => $spouse->fresh()
        ];
    }
    
    public function getEffectiveFamilyNumber($member)
    {
        // Return current family number based on marital status
        if ($member->marital_status === 'married' && $member->current_family_no) {
            return $member->current_family_no;
        }
        return $member->family_no;
    }
    
    public function getFamilyMembers($familyNo, $includeMarriedMembers = true)
    {
        $query = Member::where(function ($q) use ($familyNo) {
            $q->where('family_no', $familyNo)
              ->orWhere('current_family_no', $familyNo);
        });
        
        if (!$includeMarriedMembers) {
            $query->where('marital_status', '!=', 'married');
        }
        
        return $query->with(['relationship', 'designation', 'community'])
                    ->orderBy('member_sequence')
                    ->get();
    }
    
    public function getChurchStatistics($churchCode = null, $year = null)
    {
        $churchCode = $churchCode ?? $this->churchCode;
        $year = $year ?? date('Y');
        
        $query = Member::where('church_code', $churchCode);
        
        if ($year) {
            $query->where('registration_year', $year);
        }
        
        return [
            'total_families' => $query->distinct('family_no')->count(),
            'total_members' => $query->count(),
            'families_this_year' => $query->where('registration_year', $year)->distinct('family_no')->count(),
            'members_this_year' => $query->where('registration_year', $year)->count()
        ];
    }
    
    public function searchFamilies($query)
    {
        return Member::where('family_no', 'like', "%{$query}%")
            ->orWhere('member_no', 'like', "%{$query}%")
            ->orWhere('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"])
            ->with(['relationship', 'community'])
            ->limit(10)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'family_no' => $member->family_no,
                    'member_no' => $member->member_no,
                    'name' => $member->first_name . ' ' . $member->last_name,
                    'relationship' => $member->relationship?->name,
                    'community' => $member->community?->name
                ];
            });
    }
} 