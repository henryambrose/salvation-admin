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
                'is_default' => false,
                'language' => 'en',
            ],
            [
                'name' => 'Parochial Register Baptism Certificate',
                'certificate_type_id' => 1,
                'description' => 'Detailed baptism certificate in parochial register format with all baptismal information including godparents, minister, and cross-references to confirmation and marriage',
                'template_content' => file_get_contents(resource_path('views/certificates/templates/parochial_register_baptism.blade.php')),
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
                'is_default' => false,
                'language' => 'en',
            ],
            [
                'name' => 'Parochial Register Marriage Certificate',
                'certificate_type_id' => 3,
                'description' => 'Detailed marriage certificate in parochial register format with complete bride and bridegroom information, witnesses, and minister details',
                'template_content' => file_get_contents(resource_path('views/certificates/templates/parochial_register_marriage.blade.php')),
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
                'is_default' => false,
                'language' => 'en',
            ],
            [
                'name' => 'Parochial Register Burial Certificate',
                'certificate_type_id' => 5,
                'description' => 'Detailed burial certificate in parochial register format with death date, burial details, cause of death, and minister information',
                'template_content' => file_get_contents(resource_path('views/certificates/templates/parochial_register_burial.blade.php')),
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
