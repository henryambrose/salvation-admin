<?php

namespace Database\Seeders;

use App\Models\CellsAndAssociation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CellsAndAssociationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cellsAndAssociations = [
            ['name' => 'Al-anon ', 'description' => '1'],
            ['name' => 'Altar Services ', 'description' => '2'],
            ['name' => 'Bombay Catholic Sabha', 'description' => '3'],
            ['name' => 'Bible Cell ', 'description' => '4'],
            ['name' => 'CLM Ladies Sodality (Konkani)', 'description' => '5'],
            ['name' => 'CLM Mens Sodality (Konkani)', 'description' => '6'],
            ['name' => 'Cantors for Chirst ', 'description' => '7'],
            ['name' => 'Communication Cell ', 'description' => '8'],
            ['name' => 'Confirmation ', 'description' => '9'],
            ['name' => 'Childrens Parliament ', 'description' => '10'],
            ['name' => 'Divine Mery ', 'description' => '11'],
            ['name' => 'Décor ', 'description' => '12'],
            ['name' => 'Eucharistic Ministers', 'description' => '13'],
            ['name' => 'Helmet of Salvation ', 'description' => '14'],
            ['name' => 'HOPE AND LIFE', 'description' => '15'],
            ['name' => 'IRD - Inter Religious Dialogue', 'description' => '16'],
            ['name' => 'Joyful Proclaimers', 'description' => '17'],
            ['name' => 'Konkani Prayer Group ', 'description' => '18'],
            ['name' => 'Legion of Mary - Juniors', 'description' => '19'],
            ['name' => 'Legion of Mary - Seniors', 'description' => '20'],
            ['name' => 'PLT ', 'description' => '21'],
            ['name' => 'PACT ', 'description' => '22'],
            ['name' => 'Prison Ministry ', 'description' => '23'],
            ['name' => ' Pre- Baptismal', 'description' => '24'],
            ['name' => 'Salvation Green Cell ', 'description' => '25'],
            ['name' => 'Salvation PYC ', 'description' => '26'],
            ['name' => 'Sunday School ', 'description' => '27'],
            ['name' => 'St. Xavier (Konkani Choir)', 'description' => '28'],
            ['name' => 'St. Joseph Choral Soctiey ', 'description' => '29'],
            ['name' => 'Senior Care ', 'description' => '30'],
            ['name' => 'Senior Citizens', 'description' => '31'],
            ['name' => 'Sunday Liturgy', 'description' => '32'],
            ['name' => 'SVP - St. Vincent de Paul', 'description' => '33'],
            ['name' => 'Ushers', 'description' => '34'],
            ['name' => 'Womens Cell ', 'description' => '35'],
            ];
        CellsAndAssociation::insert($cellsAndAssociations);
    }
}
