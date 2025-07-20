<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory;

    protected $fillable = [
        'community_id',
        'community_cluster_id',
        'new_olsc_id',
        'old_sal_id',
        'aadhar',
        'family_no',
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
        'marriage_date',
        'marriage_reg_no',
        'marriage_parish',
        'death_date',
        'deaths_reg_no',
        'death_parish',
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
