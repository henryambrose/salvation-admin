<?php

namespace Modules\Graveyard\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Graveyard\Models\GraveBooking;
use Modules\Graveyard\Models\PermanentGrave;
use Modules\Graveyard\Models\TemporaryGrave;
use Modules\Members\Models\Member;
use Modules\Members\Models\Gender;
use Modules\Members\Models\Parish;
use Modules\Fund\Models\PaymentMethod;
use Modules\Members\Models\User;
use Carbon\Carbon;

class BurialBookingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get reference data
        $genders = Gender::all();
        $parishes = Parish::all();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $users = User::all();
        $members = Member::take(20)->get();

        // Get available graves
        $permanentGraves = PermanentGrave::take(15)->get();
        $temporaryGraves = TemporaryGrave::take(10)->get();

        $burialData = [
            // Recent burials
            [
                'grave_type' => 'permanent',
                'dead_first_name' => 'Mary',
                'dead_last_name' => 'Joseph',
                'date_of_birth' => '1945-03-15',
                'age' => 78,
                'months' => 8,
                'days' => 12,
                'died_on' => '2025-01-20',
                'buried_on' => '2025-01-22',
                'cause_of_death' => 'Natural causes',
                'nationality' => 'Indian',
                'relationship' => 'member',
                'applicant_type' => 'member',
                'contact_no' => '+91 9876543210',
                'permit_no' => 'BMC/2025/001',
                'status' => 'completed',
                'total_amount' => 15000.00,
                'payment_status' => 'paid',
                'payment_remarks' => 'Full payment received',
            ],
            [
                'grave_type' => 'temporary',
                'dead_first_name' => 'John',
                'dead_last_name' => 'D\'Souza',
                'date_of_birth' => '1960-07-22',
                'age' => 64,
                'months' => 5,
                'days' => 28,
                'died_on' => '2025-01-18',
                'buried_on' => '2025-01-20',
                'cause_of_death' => 'Heart attack',
                'nationality' => 'Indian',
                'relationship' => 'non_member',
                'applicant_type' => 'non_member',
                'applicant_name' => 'Susan D\'Souza',
                'contact_no' => '+91 9876543211',
                'permit_no' => 'BMC/2025/002',
                'status' => 'completed',
                'total_amount' => 8000.00,
                'payment_status' => 'paid',
            ],
            [
                'grave_type' => 'permanent',
                'dead_first_name' => 'Anthony',
                'dead_last_name' => 'Fernandes',
                'date_of_birth' => '1935-12-08',
                'age' => 89,
                'months' => 1,
                'days' => 10,
                'died_on' => '2025-01-15',
                'buried_on' => '2025-01-17',
                'cause_of_death' => 'Old age',
                'nationality' => 'Indian',
                'relationship' => 'member',
                'applicant_type' => 'member',
                'contact_no' => '+91 9876543212',
                'permit_no' => 'BMC/2025/003',
                'status' => 'completed',
                'total_amount' => 12000.00,
                'payment_status' => 'paid',
            ],
            // Pending burials
            [
                'grave_type' => 'permanent',
                'dead_first_name' => 'Maria',
                'dead_last_name' => 'Rodrigues',
                'date_of_birth' => '1955-09-14',
                'age' => 69,
                'months' => 4,
                'days' => 5,
                'died_on' => '2025-01-25',
                'buried_on' => '2025-01-27',
                'cause_of_death' => 'Cancer',
                'nationality' => 'Indian',
                'relationship' => 'member',
                'applicant_type' => 'member',
                'contact_no' => '+91 9876543213',
                'permit_no' => 'BMC/2025/004',
                'status' => 'confirmed',
                'total_amount' => 18000.00,
                'payment_status' => 'partial',
                'payment_remarks' => '10000 paid, balance pending',
            ],
            [
                'grave_type' => 'temporary',
                'dead_first_name' => 'Peter',
                'dead_last_name' => 'Dias',
                'date_of_birth' => '1970-04-30',
                'age' => 54,
                'months' => 8,
                'days' => 25,
                'died_on' => '2025-01-26',
                'buried_on' => '2025-01-28',
                'cause_of_death' => 'Accident',
                'nationality' => 'Indian',
                'relationship' => 'non_member',
                'applicant_type' => 'non_member',
                'applicant_name' => 'Rita Dias',
                'contact_no' => '+91 9876543214',
                'permit_no' => 'BMC/2025/005',
                'status' => 'pending',
                'total_amount' => 10000.00,
                'payment_status' => 'pending',
            ],
            // More sample data
            [
                'grave_type' => 'permanent',
                'dead_first_name' => 'Catherine',
                'dead_last_name' => 'Pereira',
                'date_of_birth' => '1948-11-03',
                'age' => 76,
                'months' => 2,
                'days' => 15,
                'died_on' => '2025-01-10',
                'buried_on' => '2025-01-12',
                'cause_of_death' => 'Pneumonia',
                'nationality' => 'Indian',
                'relationship' => 'member',
                'applicant_type' => 'member',
                'contact_no' => '+91 9876543215',
                'permit_no' => 'BMC/2025/006',
                'status' => 'completed',
                'total_amount' => 14000.00,
                'payment_status' => 'paid',
            ],
            [
                'grave_type' => 'temporary',
                'dead_first_name' => 'Francis',
                'dead_last_name' => 'Gomes',
                'date_of_birth' => '1965-06-18',
                'age' => 59,
                'months' => 7,
                'days' => 8,
                'died_on' => '2025-01-08',
                'buried_on' => '2025-01-10',
                'cause_of_death' => 'Stroke',
                'nationality' => 'Indian',
                'relationship' => 'non_member',
                'applicant_type' => 'member',
                'contact_no' => '+91 9876543216',
                'permit_no' => 'BMC/2025/007',
                'status' => 'completed',
                'total_amount' => 9000.00,
                'payment_status' => 'paid',
            ],
            [
                'grave_type' => 'permanent',
                'dead_first_name' => 'Rose',
                'dead_last_name' => 'Miranda',
                'date_of_birth' => '1952-01-25',
                'age' => 72,
                'months' => 11,
                'days' => 30,
                'died_on' => '2025-01-05',
                'buried_on' => '2025-01-07',
                'cause_of_death' => 'Kidney failure',
                'nationality' => 'Indian',
                'relationship' => 'member',
                'applicant_type' => 'member',
                'contact_no' => '+91 9876543217',
                'permit_no' => 'BMC/2025/008',
                'status' => 'completed',
                'total_amount' => 16000.00,
                'payment_status' => 'paid',
            ],
            // Archived/deleted records
            [
                'grave_type' => 'temporary',
                'dead_first_name' => 'James',
                'dead_last_name' => 'Lobo',
                'date_of_birth' => '1980-08-12',
                'age' => 44,
                'months' => 5,
                'days' => 3,
                'died_on' => '2024-12-28',
                'buried_on' => '2024-12-30',
                'cause_of_death' => 'COVID-19',
                'nationality' => 'Indian',
                'relationship' => 'non_member',
                'applicant_type' => 'non_member',
                'applicant_name' => 'Joyce Lobo',
                'contact_no' => '+91 9876543218',
                'permit_no' => 'BMC/2024/099',
                'status' => 'completed',
                'total_amount' => 7500.00,
                'payment_status' => 'paid',
                'deleted_at' => '2025-01-15 10:30:00',
            ],
            [
                'grave_type' => 'permanent',
                'dead_first_name' => 'Agnes',
                'dead_last_name' => 'Sequeira',
                'date_of_birth' => '1940-02-14',
                'age' => 84,
                'months' => 10,
                'days' => 14,
                'died_on' => '2024-12-25',
                'buried_on' => '2024-12-27',
                'cause_of_death' => 'Natural causes',
                'nationality' => 'Indian',
                'relationship' => 'member',
                'applicant_type' => 'member',
                'contact_no' => '+91 9876543219',
                'permit_no' => 'BMC/2024/098',
                'status' => 'completed',
                'total_amount' => 13500.00,
                'payment_status' => 'paid',
                'deleted_at' => '2025-01-20 14:45:00',
            ],
        ];

        foreach ($burialData as $index => $burial) {
            // Assign gender
            $burial['gender_id'] = $genders->random()->id;
            
            // Assign parish
            $burial['parish_id'] = $parishes->random()->id;
            
            // Assign payment method
            $burial['payment_method_id'] = $paymentMethods->random()->id;
            
            // Assign creator/updater
            $burial['created_by'] = $users->random()->id;
            $burial['updated_by'] = $burial['created_by'];
            
            // Assign graves
            if ($burial['grave_type'] === 'permanent' && $permanentGraves->count() > 0) {
                $burial['permanent_grave_id'] = $permanentGraves->random()->id;
            } elseif ($burial['grave_type'] === 'temporary' && $temporaryGraves->count() > 0) {
                $burial['temporary_grave_id'] = $temporaryGraves->random()->id;
            }
            
            // Assign member relationships
            if ($burial['relationship'] === 'member' && $members->count() > 0) {
                $burial['member_id'] = $members->random()->id;
            }
            
            if ($burial['applicant_type'] === 'member' && $members->count() > 0) {
                $burial['applicant_member_id'] = $members->random()->id;
            }
            
            // Calculate minister name for parish
            $burial['minister'] = 'Fr. ' . ['Anthony', 'Joseph', 'Peter', 'Francis', 'Michael', 'John'][rand(0, 5)] . ' ' . ['D\'Souza', 'Fernandes', 'Rodrigues', 'Pereira', 'Gomes', 'Miranda'][rand(0, 5)];
            
            // Set timestamps
            $burial['created_at'] = Carbon::parse($burial['buried_on'])->addDays(rand(1, 3));
            $burial['updated_at'] = $burial['created_at'];
            
            GraveBooking::create($burial);
        }

        $this->command->info('Burial bookings seeded successfully!');
    }
}

