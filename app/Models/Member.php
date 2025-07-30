<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'community_id',
        'community_cluster_id',
        'new_olsc_id',
        'old_sal_id',
        'aadhar',
        'family_no',
        'member_no',
        'registration_year',
        'church_code',
        'family_sequence',
        'member_sequence',
        'marital_status',
        'current_family_no',
        'spouse_member_id',
        'marriage_date',
        'status_id',
        'relationship_id',
        'last_name',
        'first_name',
        'middle_name',
        'gender_id',
        'date_of_birth',
        'permanent_add1',
        'permanent_add2',
        'permanent_add3',
        'permanent_town_id',
        'permanent_pincode',
        'permanent_state_id',
        'permanent_country_id',
        'current_add1',
        'current_add2',
        'current_add3',
        'current_town_id',
        'current_pincode',
        'current_state_id',
        'current_country_id',
        'contact_no',
        'email',
        'blood_group_id',
        'cells_and_association_id',
        'school_name',
        'college_name',
        'latest_qualifications',
        'company_name',
        'designation_id',
        'family_income_range_id',
        'baptism_date',
        'baptism_reg_no',
        'baptism_parish',
        'confirmation_date',
        'confirmation_reg_no',
        'confirmation_parish',
        'marriage_reg_no',
        'marriage_parish',
        'death_date',
        'deaths_reg_no',
        'death_parish',
    ];

    protected $casts = [
        'marriage_date' => 'date',
        'date_of_birth' => 'date',
        'baptism_date' => 'date',
        'confirmation_date' => 'date',
        'death_date' => 'date'
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
    public function communityCluster()
    {
        return $this->belongsTo(CommunityCluster::class);
    }
    public function cellsAndAssociation()
    {
        return $this->belongsTo(CellsAndAssociation::class);
    }

    // relationship
    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }

    public function relationships()
    {
        return $this->hasMany(FamilyLink::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }
    public function bloodGroup()
    {
        return $this->belongsTo(BloodGroup::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($member) {
            if (!$member->family_no) {
                $numberingService = new \App\Services\FamilyNumberingService($member->church_code ?? 'SAL');
                
                // Generate family number
                $member->family_no = $numberingService->generateFamilyNumber(
                    $member->registration_year ?? date('Y'),
                    $member->church_code ?? 'SAL'
                );
                
                // Parse family number to extract components
                $familyInfo = $numberingService->parseFamilyNumber($member->family_no);
                $member->family_sequence = $familyInfo['family_sequence'];
                $member->registration_year = $familyInfo['year'];
                $member->church_code = $familyInfo['church_code'];
                
                // Generate member number
                $member->member_no = $numberingService->generateMemberNumber(
                    $member->family_no,
                    $member->family_sequence
                );
                
                // Set member sequence
                $member->member_sequence = (int) explode('-', $member->member_no)[1];
            }
        });
    }
    
    public function getEffectiveFamilyNumberAttribute()
    {
        $numberingService = new \App\Services\FamilyNumberingService($this->church_code);
        return $numberingService->getEffectiveFamilyNumber($this);
    }
    
    public function getDisplayFamilyNumberAttribute()
    {
        $display = $this->effective_family_number;
        
        if ($this->family_no !== $this->effective_family_number) {
            $display .= " (née " . $this->family_no . ")";
        }
        
        return $display;
    }
    
    public function getChurchNameAttribute()
    {
        $numberingService = new \App\Services\FamilyNumberingService($this->church_code);
        $familyInfo = $numberingService->getFamilyInfo($this->family_no);
        return $familyInfo['church_name'];
    }
    
    public function birthFamily()
    {
        return $this->belongsTo(Member::class, 'family_no', 'family_no');
    }
    
    public function currentFamily()
    {
        return $this->belongsTo(Member::class, 'current_family_no', 'family_no');
    }
    
    public function spouse()
    {
        return $this->belongsTo(Member::class, 'spouse_member_id');
    }
    
    public function familyMembers()
    {
        $numberingService = new \App\Services\FamilyNumberingService($this->church_code);
        return $numberingService->getFamilyMembers($this->effective_family_number);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function familyIncomeRange()
    {
        return $this->belongsTo(FamilyIncomeRange::class);
    }

    public function relatedMembers()
    {
        return $this->belongsToMany(Member::class, 'family_links', 'member_id', 'related_member_id')
                    ->withPivot('relationship_id');
    }
}
