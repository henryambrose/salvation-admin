<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberSeederOptimized extends Seeder
{
    public function run(): void
    {
        // Process data in chunks to avoid memory issues
        $chunkSize = 100;
        $members = $this->getMembersData();
        
        // Process in chunks
        foreach (array_chunk($members, $chunkSize) as $chunk) {
            DB::table('members')->insert($chunk);
        }
    }

    private function getMembersData(): array
    {
        return [
            ['community_id'=>'1','community_cluster_id'=>'5','old_family_no'=>'98','aadhar'=>'424252520001','registration_year'=>'2025','first_name'=>'Abhilasha','middle_name'=>'Michael','last_name'=>'Reddy','date_of_birth'=>'1993.08.11','permanent_add1'=>'105/1C, Mhada Colony,','permanent_add2'=>'PADD2','permanent_add3'=>'PADD3','permanent_town_id'=>'2','permanent_city_id'=>'2','permanent_state_id'=>'22','permanent_country_id'=>'96','permanent_pincode'=>'400028','current_add1'=>'CADD1','current_add2'=>'CADD1','current_add3'=>'CADD3','current_town_id'=>'2','current_city_id'=>'2','current_state_id'=>'22','current_country_id'=>'96','current_pincode'=>'400028','contact_no_1'=>'8433856570','contact_no_2'=>null,'email'=>'e0000001@olscemail.com','blood_group_id'=>'1','school_name'=>'Holy Cross High School','college_name'=>'St. Xavier\'s College','latest_qualifications'=>'Architect','company_name'=>'Godrej Industries Limited','income_range_id'=>'2','baptism_date'=>'1973.05.05','baptism_reg_no'=>'B00001','baptism_parish'=>null,'confirmation_date'=>'1978.12.08','confirmation_reg_no'=>'C-00001','confirmation_parish'=>null,'marriage_date'=>'1946.07.13','marriage_reg_no'=>'M-00001','marriage_parish'=>null,'death_date'=>null,'deaths_reg_no'=>null,'death_parish'=>null,'marital_status'=>null,'parish_id'=>'16','designation_id'=>'Estate Agent','gender_id'=>'2','status_id'=>'1','relationship_id'=>'1','baptism_parish_id'=>'16','confirmation_parish_id'=>'16','marriage_parish_id'=>'16','death_parish_id'=>'16'],
            ['community_id'=>'1','community_cluster_id'=>'5','old_family_no'=>'98','aadhar'=>'424252520002','registration_year'=>'2025','first_name'=>'Aaron','middle_name'=>'Michael','last_name'=>'Reddy','date_of_birth'=>'2019.03.09','permanent_add1'=>'105/1C, Mhada Colony,','permanent_add2'=>'PADD2','permanent_add3'=>'PADD3','permanent_town_id'=>'2','permanent_city_id'=>'2','permanent_state_id'=>'22','permanent_country_id'=>'96','permanent_pincode'=>'400028','current_add1'=>'CADD1','current_add2'=>'CADD1','current_add3'=>'CADD3','current_town_id'=>'2','current_city_id'=>'2','current_state_id'=>'22','current_country_id'=>'96','current_pincode'=>'400028','contact_no_1'=>null,'contact_no_2'=>null,'email'=>'e0000002@olscemail.com','blood_group_id'=>'2','school_name'=>'King George','college_name'=>'St.Andrew\'s College of Art\'s','latest_qualifications'=>'Civil Contractor','company_name'=>'Siemens Limited','income_range_id'=>'1','baptism_date'=>'2019.03.24','baptism_reg_no'=>'B00002','baptism_parish'=>null,'confirmation_date'=>'2013.12.08','confirmation_reg_no'=>'C-00002','confirmation_parish'=>null,'marriage_date'=>'2038.01.25','marriage_reg_no'=>'M-00002','marriage_parish'=>null,'death_date'=>'2063.03.22','deaths_reg_no'=>'D-00002','death_parish'=>null,'marital_status'=>null,'parish_id'=>'16','designation_id'=>'Retired','gender_id'=>'1','status_id'=>'1','relationship_id'=>'4','baptism_parish_id'=>'16','confirmation_parish_id'=>'16','marriage_parish_id'=>'16','death_parish_id'=>'16'],
            // Add more members here as needed, but keep the array manageable
        ];
    }
} 