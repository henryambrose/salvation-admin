<?php

namespace Modules\Members\Database\Seeders;


use Illuminate\Database\Seeder;
use Modules\Members\Models\CertificateType;

class CertificateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $certificateTypes = [
            [
                'name' => 'Baptism Certificate',
                'code' => 'baptism',
                'description' => 'Certificate issued for baptism sacrament',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Confirmation Certificate',
                'code' => 'confirmation',
                'description' => 'Certificate issued for confirmation sacrament',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Marriage Certificate',
                'code' => 'marriage',
                'description' => 'Certificate issued for marriage sacrament',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Membership Certificate',
                'code' => 'membership',
                'description' => 'Certificate issued for church membership',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Death Certificate',
                'code' => 'death',
                'description' => 'Certificate issued for death records',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($certificateTypes as $type) {
            CertificateType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
