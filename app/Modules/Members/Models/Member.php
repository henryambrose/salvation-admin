<?php

namespace Modules\Members\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Members\Models\Relationship;
use Modules\Members\Models\FamilyPhoto;
use Modules\Members\Services\FamilyNumberingService;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'community_id',
        'community_cluster_id',
        'old_family_no',
        'aadhar',
        'birth_family_no',
        'family_no',
        'member_no',
        'registration_year',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'permanent_add1',
        'permanent_add2',
        'permanent_add3',
        'permanent_town_id',
        'permanent_city_id',
        'permanent_state_id',
        'permanent_country_id',
        'permanent_pincode',
        'current_add1',
        'current_add2',
        'current_add3',
        'current_town_id',
        'current_city_id',
        'current_state_id',
        'current_country_id',
        'current_pincode',
        'contact_no_1',
        'contact_no_2',
        'email',
        'blood_group_id',
        'school_name',
        'college_name',
        'latest_qualifications',
        'company_name',
        'income_range_id',
        'baptism_date',
        'baptism_reg_no',
        'baptism_parish',
        'confirmation_date',
        'confirmation_reg_no',
        'confirmation_parish',
        'marriage_date',
        'marriage_reg_no',
        'marriage_parish',
        'death_date',
        'deaths_reg_no',
        'death_parish',
        'family_sequence',
        'member_sequence',
        'marital_status',
        'relation_member_id', // Keep for backward compatibility
        'mother_id', // New field
        'father_id', // New field
        'spouse_id', // New field
        'father_source',
        'mother_source',
        'spouse_source',
        'relationship_id',
        'uid',
        'gender_id',
        'parish_id',
        'designation_id',
        'status_id',
        'father_source',
        'mother_source',
        'spouse_source',
    ];

    protected $casts = [
        'marriage_date' => 'date',
        'date_of_birth' => 'date',
        'baptism_date' => 'date',
        'confirmation_date' => 'date',
        'death_date' => 'date',
    ];

    /**
     * Scope members to the allowed communities for the given user (ppc/scc heads).
     * Returns unmodified query if unrestricted.
     */
    public function scopeForUserCommunities($query, $user)
    {
        $service = new \Modules\Members\Services\CommunityAccessService;
        $allowed = $service->getAllowedCommunityIds($user);
        if ($allowed === null) {
            return $query; // unrestricted
        }
        if (empty($allowed)) {
            return $query->whereRaw('1=0');
        }

        return $query->whereIn('community_id', $allowed);
    }

    /**
     * Get the member's full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . ($this->last_name ?? ''));
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function communityCluster()
    {
        return $this->belongsTo(CommunityCluster::class);
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
            $churchCode = config('app.church_code', 'SAL');
            $numberingService = new FamilyNumberingService($churchCode);

            // Set default values
            $member->registration_year = $member->registration_year ?? date('Y');

            // Generate independent member number if not set
            if (! $member->member_no) {
                $member->member_no = $numberingService->generateMemberNumber(
                    $member->registration_year,
                    $churchCode
                );
            }

            // Generate family number if not set
            if (! $member->family_no) {
                $member->family_no = $numberingService->generateFamilyGroupNumber(
                    $churchCode
                );
            }

            // Parse member number to extract components
            if ($member->member_no) {
                $memberInfo = $numberingService->parseMemberNumber($member->member_no);
                $member->registration_year = $memberInfo['year'];
                $member->member_sequence = $memberInfo['member_sequence'];
            }

            // Parse family number to extract components
            if ($member->family_no) {
                $familyInfo = $numberingService->parseFamilyNumber($member->family_no);
                $member->family_sequence = $familyInfo['family_group'];
            }
        });
    }

    public function getEffectiveFamilyNumberAttribute()
    {
        $churchCode = config('app.church_code', 'SAL');
        $numberingService = new FamilyNumberingService($churchCode);

        return $numberingService->getEffectiveFamilyNumber($this);
    }

    public function getDisplayFamilyNumberAttribute()
    {
        $display = $this->effective_family_number;

        if ($this->family_no !== $this->effective_family_number) {
            $display .= ' (née ' . $this->family_no . ')';
        }

        return $display;
    }

    public function getChurchNameAttribute()
    {
        $churchCode = config('app.church_code', 'SAL');
        $numberingService = new FamilyNumberingService($churchCode);
        $familyInfo = $numberingService->getFamilyInfo($this->family_no);

        return $familyInfo['church_name'];
    }

    public function currentFamily()
    {
        return $this->belongsTo(Member::class, 'family_no', 'family_no');
    }

    public function birthFamily()
    {
        return $this->belongsTo(Member::class, 'birth_family_no', 'family_no');
    }

    /**
     * Get the member's mother
     */
    public function mother()
    {
        return $this->belongsTo(Member::class, 'mother_id');
    }

    /**
     * Get the member's father
     */
    public function father()
    {
        return $this->belongsTo(Member::class, 'father_id');
    }

    /**
     * Get the member's spouse
     */
    public function spouse()
    {
        return $this->belongsTo(Member::class, 'spouse_id');
    }

    public function familyMembers()
    {
        $churchCode = config('app.church_code', 'SAL');
        $numberingService =  new FamilyNumberingService($churchCode);

        return $numberingService->getFamilyMembers($this->effective_family_number);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function incomeRange()
    {
        return $this->belongsTo(IncomeRange::class);
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }


    public function baptismParish()
    {
        return $this->belongsTo(Parish::class, 'baptism_parish_id');
    }

    public function confirmationParish()
    {
        return $this->belongsTo(Parish::class, 'confirmation_parish_id');
    }

    public function marriageParish()
    {
        return $this->belongsTo(Parish::class, 'marriage_parish_id');
    }

    public function deathParish()
    {
        return $this->belongsTo(Parish::class, 'death_parish_id');
    }

    public function permanentTown()
    {
        return $this->belongsTo(Town::class, 'permanent_town_id');
    }

    public function permanentCity()
    {
        return $this->belongsTo(City::class, 'permanent_city_id');
    }

    public function permanentState()
    {
        return $this->belongsTo(State::class, 'permanent_state_id');
    }

    public function permanentCountry()
    {
        return $this->belongsTo(Country::class, 'permanent_country_id');
    }

    public function currentTown()
    {
        return $this->belongsTo(Town::class, 'current_town_id');
    }

    public function currentCity()
    {
        return $this->belongsTo(City::class, 'current_city_id');
    }

    public function currentState()
    {
        return $this->belongsTo(State::class, 'current_state_id');
    }

    public function currentCountry()
    {
        return $this->belongsTo(Country::class, 'current_country_id');
    }


    public function cellsAndAssociations()
    {
        return $this->belongsToMany(CellsAndAssociation::class, 'cells_and_association_members', 'member_id', 'cells_and_association_id');
    }

    public function sccHeads()
    {
        return $this->hasMany(SCCHead::class);
    }

    public function ppcHeads()
    {
        return $this->hasMany(PPCHead::class);
    }

    public function clusterHeads()
    {
        return $this->hasMany(CommunityCluster::class, 'member_id');
    }

    /**
     * Get the family photo for this member's family
     */
    public function familyPhoto()
    {
        return $this->hasOne(FamilyPhoto::class, 'family_no', 'family_no');
    }

    public function getUidAttribute(): string
    {
        return 'M-' . $this->id;
    }

    // Add these query scopes
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('contact_no_1', 'like', "%{$search}%");
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status_id', function ($q) {
            $q->select('id')->from('statuses')->where('name', 'Active');
        });
    }

    public function scopeInactive($query)
    {
        return $query->where('status_id', function ($q) {
            $q->select('id')->from('statuses')->where('name', 'Inactive');
        });
    }

    // Add these missing scopes
    public function scopeByCommunity($query, $communityId)
    {
        return $query->where('community_id', $communityId);
    }

    public function scopeByZone($query, $zoneId)
    {
        return $query->where('current_zone_id', $zoneId);
    }

    public function scopeByCity($query, $cityId)
    {
        return $query->where('current_city_id', $cityId);
    }

    public function scopeByState($query, $stateId)
    {
        return $query->where('current_state_id', $stateId);
    }

    public function scopeByCountry($query, $countryId)
    {
        return $query->where('current_country_id', $countryId);
    }

    public function scopeByParish($query, $parishId)
    {
        return $query->where('parish_id', $parishId);
    }

    public function scopeByDesignation($query, $designationId)
    {
        return $query->where('designation_id', $designationId);
    }

    public function scopeByGender($query, $genderId)
    {
        return $query->where('gender_id', $genderId);
    }

    public function scopeByAgeGroup($query, $ageGroupId)
    {
        return $query->where('age_group_id', $ageGroupId);
    }

    public function scopeByBloodGroup($query, $bloodGroupId)
    {
        return $query->where('blood_group_id', $bloodGroupId);
    }

    public function scopeByIncomeRange($query, $incomeRangeId)
    {
        return $query->where('income_range_id', $incomeRangeId);
    }

    public function scopeByStatus($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    /**
     * Scope to get only alive members (no death_date)
     */
    public function scopeAlive($query)
    {
        return $query->whereNull('death_date');
    }

    /**
     * Scope to get only deceased members (has death_date)
     */
    public function scopeDeceased($query)
    {
        return $query->whereNotNull('death_date');
    }

    public function scopeWithValidFamilyNo($query)
    {
        return $query->whereNotNull('family_no')
            ->whereRaw('TRIM(family_no) != ?', ['']);
    }
    /**
     * Scope to conditionally exclude deceased members
     */
    public function scopeExcludeDeceasedIf($query, $condition)
    {
        return $condition ? $query->alive() : $query;
    }
}
