<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\State;
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
            ['name' => 'Other', 'abbr' => '', 'country_id' => '1' ],
            ['name' => 'Andaman and Nicobar Islands', 'abbr' => 'AN', 'country_id' => '96' ],
            ['name' => 'Andhra Pradesh', 'abbr' => 'AP', 'country_id' => '96' ],
            ['name' => 'Arunachal Pradesh', 'abbr' => 'AR', 'country_id' => '96' ],
            ['name' => 'Assam', 'abbr' => 'AS', 'country_id' => '96' ],
            ['name' => 'Bihar', 'abbr' => 'BR', 'country_id' => '96' ],
            ['name' => 'Chandigarh', 'abbr' => 'CH', 'country_id' => '96' ],
            ['name' => 'Chhattisgarh', 'abbr' => 'CG', 'country_id' => '96' ],
            ['name' => 'Delhi', 'abbr' => 'DL', 'country_id' => '96' ],
            ['name' => 'Dadra and Nagar Haveli and Daman and Diu', 'abbr' => 'DH', 'country_id' => '96' ],
            ['name' => 'Goa', 'abbr' => 'GA', 'country_id' => '96' ],
            ['name' => 'Gujarat', 'abbr' => 'GJ', 'country_id' => '96' ],
            ['name' => 'Haryana', 'abbr' => 'HR', 'country_id' => '96' ],
            ['name' => 'Himachal Pradesh', 'abbr' => 'HP', 'country_id' => '96' ],
            ['name' => 'Jammu and Kashmir', 'abbr' => 'JK', 'country_id' => '96' ],
            ['name' => 'Jharkhand', 'abbr' => 'JH', 'country_id' => '96' ],
            ['name' => 'Karnataka', 'abbr' => 'KA', 'country_id' => '96' ],
            ['name' => 'Kerala', 'abbr' => 'KL', 'country_id' => '96' ],
            ['name' => 'Ladakh', 'abbr' => 'LA', 'country_id' => '96' ],
            ['name' => 'Lakshadweep', 'abbr' => 'LD', 'country_id' => '96' ],
            ['name' => 'Madhya Pradesh', 'abbr' => 'MP', 'country_id' => '96' ],
            ['name' => 'Maharashtra', 'abbr' => 'MH', 'country_id' => '96' ],
            ['name' => 'Manipur', 'abbr' => 'MN', 'country_id' => '96' ],
            ['name' => 'Meghalaya', 'abbr' => 'Ml', 'country_id' => '96' ],
            ['name' => 'Mizoram', 'abbr' => 'ML', 'country_id' => '96' ],
            ['name' => 'Nagaland', 'abbr' => 'NL', 'country_id' => '96' ],
            ['name' => 'Odisha', 'abbr' => 'OD', 'country_id' => '96' ],
            ['name' => 'Puducherry', 'abbr' => 'PY', 'country_id' => '96' ],
            ['name' => 'Punjab', 'abbr' => 'PB', 'country_id' => '96' ],
            ['name' => 'Rajasthan', 'abbr' => 'RJ', 'country_id' => '96' ],
            ['name' => 'Sikkim', 'abbr' => 'SK', 'country_id' => '96' ],
            ['name' => 'Tamil Nadu', 'abbr' => 'TV', 'country_id' => '96' ],
            ['name' => 'Telangana', 'abbr' => 'TG', 'country_id' => '96' ],
            ['name' => 'Tripura', 'abbr' => 'TR', 'country_id' => '96' ],
            ['name' => 'Uttar Pradesh', 'abbr' => 'UP', 'country_id' => '96' ],
            ['name' => 'Uttarakhand', 'abbr' => 'UK', 'country_id' => '96' ],
            ['name' => 'West Bengal', 'abbr' => 'WB', 'country_id' => '96' ]
        ];

        State::insert($states);
    }
}
