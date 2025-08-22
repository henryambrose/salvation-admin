<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\CellsAndAssociation;
use Illuminate\Database\Seeder;

class CellsAndAssociationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cellsAndAssociations = [
            ['name' => 'Al-anon'],
            ['name' => 'Altar Services'],
            ['name' => 'Bombay Catholic Sabha'],
            ['name' => 'Bible Cell'],
            ['name' => 'CLM Ladies Sodality (Konkani)'],
            ['name' => 'CLM Mens Sodality (Konkani)'],
            ['name' => 'Cantors for Chirst'],
            ['name' => 'Communication Cell'],
            ['name' => 'Confirmation'],
            ['name' => 'Childrens Parliament'],
            ['name' => 'Divine Mery'],
            ['name' => 'Décor'],
            ['name' => 'Eucharistic Ministers'],
            ['name' => 'Helmet of Salvation'],
            ['name' => 'HOPE AND LIFE'],
            ['name' => 'IRD - Inter Religious Dialogue'],
            ['name' => 'Joyful Proclaimers'],
            ['name' => 'Konkani Prayer Group '],
            ['name' => 'Legion of Mary - Juniors'],
            ['name' => 'Legion of Mary - Seniors'],
            ['name' => 'PLT'],
            ['name' => 'PACT'],
            ['name' => 'Prison Ministry'],
            ['name' => ' Pre- Baptismal'],
            ['name' => 'Salvation Green Cell'],
            ['name' => 'Salvation PYC'],
            ['name' => 'Sunday School'],
            ['name' => 'St. Xavier (Konkani Choir)'],
            ['name' => 'St. Joseph Choral Soctiey'],
            ['name' => 'Senior Care'],
            ['name' => 'Senior Citizens'],
            ['name' => 'Sunday Liturgy'],
            ['name' => 'SVP - St. Vincent de Paul'],
            ['name' => 'Ushers'],
            ['name' => 'Womens Cell '],
        ];
        foreach ($cellsAndAssociations as $cellsAndAssociation) {
            CellsAndAssociation::updateOrCreate(
                ['name' => $cellsAndAssociation['name']],
                $cellsAndAssociation
            );
        }
    }
}
