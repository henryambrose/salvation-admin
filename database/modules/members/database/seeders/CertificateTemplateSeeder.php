<?php

namespace Modules\Members\Database\Seeders;


use Illuminate\Database\Seeder;
use Modules\Members\Models\CertificateTemplate;


class CertificateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Default Baptism Certificate',
                'certificate_type_id' => 1,
                'description' => 'Standard baptism certificate template',
                'is_active' => true,
                'is_default' => true,
                'language' => 'en',
            ],
            [
                'name' => 'Default Confirmation Certificate',
                'certificate_type_id' => 2,
                'description' => 'Standard confirmation certificate template',
                'is_active' => true,
                'is_default' => true,
                'language' => 'en',
            ],
            [
                'name' => 'Default Marriage Certificate',
                'certificate_type_id' => 3,
                'description' => 'Standard marriage certificate template',
                'is_active' => true,
                'is_default' => true,
                'language' => 'en',
            ],
            [
                'name' => 'Default Membership Certificate',
                'certificate_type_id' => 4,
                'description' => 'Standard membership certificate template',
                'is_active' => true,
                'is_default' => true,
                'language' => 'en',
            ],
            [
                'name' => 'Default Death Certificate',
                'certificate_type_id' => 5,
                'description' => 'Standard death certificate template',
                'is_active' => true,
                'is_default' => true,
                'language' => 'en',
            ],
        ];

        foreach ($templates as $templateData) {

            CertificateTemplate::firstOrCreate($templateData);
        }
    }
}
