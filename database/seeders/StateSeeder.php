<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['name' => 'Andaman and Nicobar Islands', 'abbr' => 'AN'], 
            ['name' => 'Andhra Pradesh', 'abbr' => 'AP'], 
            ['name' => 'Arunachal Pradesh', 'abbr' => 'AR'], 
            ['name' => 'Assam', 'abbr' => 'AS'], 
            ['name' => 'Bihar', 'abbr' => 'BR'], 
            ['name' => 'Chandigarh', 'abbr' => 'CH'], 
            ['name' => 'Chhattisgarh', 'abbr' => 'CG'], 
            ['name' => 'Delhi', 'abbr' => 'DL'], 
            ['name' => 'Dadra and Nagar Haveli and Daman and Diu', 'abbr' => 'DH'], 
            ['name' => 'Goa', 'abbr' => 'GA'], 
            ['name' => 'Gujarat', 'abbr' => 'GJ'], 
            ['name' => 'Haryana', 'abbr' => 'HR'], 
            ['name' => 'Himachal Pradesh', 'abbr' => 'HP'], 
            ['name' => 'Jammu and Kashmir', 'abbr' => 'JK'], 
            ['name' => 'Jharkhand', 'abbr' => 'JH'], 
            ['name' => 'Karnataka', 'abbr' => 'KA'], 
            ['name' => 'Kerala', 'abbr' => 'KL'], 
            ['name' => 'Ladakh', 'abbr' => 'LA'], 
            ['name' => 'Lakshadweep', 'abbr' => 'LD'], 
            ['name' => 'Madhya Pradesh', 'abbr' => 'MP'], 
            ['name' => 'Maharashtra', 'abbr' => 'MH'], 
            ['name' => 'Manipur', 'abbr' => 'MN'], 
            ['name' => 'Meghalaya', 'abbr' => 'Ml'], 
            ['name' => 'Mizoram', 'abbr' => 'ML'], 
            ['name' => 'Nagaland', 'abbr' => 'NL'], 
            ['name' => 'Odisha', 'abbr' => 'OD'], 
            ['name' => 'Puducherry', 'abbr' => 'PY'], 
            ['name' => 'Punjab', 'abbr' => 'PB'], 
            ['name' => 'Rajasthan', 'abbr' => 'RJ'], 
            ['name' => 'Sikkim', 'abbr' => 'SK'], 
            ['name' => 'Tamil Nadu', 'abbr' => 'TV'], 
            ['name' => 'Telangana', 'abbr' => 'TG'], 
            ['name' => 'Tripura', 'abbr' => 'TR'], 
            ['name' => 'Uttar Pradesh', 'abbr' => 'UP'], 
            ['name' => 'Uttarakhand', 'abbr' => 'UK'], 
            ['name' => 'West Bengal', 'abbr' => 'WB'] 
            ];

        State::insert($states);
    }
}
