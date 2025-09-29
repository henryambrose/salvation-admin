<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberSeederMemoryEfficient extends Seeder
{
    private $memberCounter = 1;
    private $familyCounter = 1;
    private $familyGroups = [];
    private $familyMemberCounters = [];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting memory-efficient member seeding...');

        // Set optimal memory and execution settings
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 0);

        // Optimize database settings for bulk insert
        DB::statement('SET SESSION sql_mode = ""');
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('SET UNIQUE_CHECKS=0');
        DB::statement('SET AUTOCOMMIT=0');

        try {
            DB::beginTransaction();

            // Process in very small chunks to avoid memory issues
            $this->processInSmallChunks();

            DB::commit();

            $this->command->info("Successfully seeded $this->memberCounter members!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Seeding failed: ' . $e->getMessage());
            throw $e;
        } finally {
            // Restore database settings
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            DB::statement('SET UNIQUE_CHECKS=1');
            DB::statement('SET AUTOCOMMIT=1');
        }
    }

    /**
     * Process members in very small chunks
     */
    private function processInSmallChunks(): void
    {
        $chunkSize = 25; // Very small chunks
        $chunk = [];
        $processed = 0;

        // Use iterator to get members one by one
        foreach ($this->getMemberIterator() as $memberData) {
            $processedMember = $this->processIndividualMember($memberData);
            $chunk[] = $processedMember;

            if (count($chunk) >= $chunkSize) {
                $this->insertChunk($chunk);
                $processed += count($chunk);

                $this->command->line("Processed: $processed members (Memory: " . $this->getMemoryUsage() . ")");

                // Clear chunk and force cleanup
                $chunk = [];
                $this->cleanup();
            }
        }

        // Insert remaining members
        if (!empty($chunk)) {
            $this->insertChunk($chunk);
            $processed += count($chunk);
            $this->command->line("Final batch: $processed members");
        }
    }

    /**
     * Iterator that yields members one at a time to minimize memory usage
     */
    private function getMemberIterator(): \Generator
    {
        // Sample members - you can expand this or load from file
        $sampleMembers = [
            ['community_id'=>'1','old_family_no'=>'134','old_sal_id'=>'SAL/01/134/01','first_name'=>'Abhilasha','middle_name'=>'Michael','last_name'=>'Reddy','date_of_birth'=>'1993-08-11','permanent_add1'=>'105/1C, Mhada Colony,','relationship_id'=>'1'],
            ['community_id'=>'1','old_family_no'=>'134','old_sal_id'=>'SAL/01/134/02','first_name'=>'Aaron','middle_name'=>'Michael','last_name'=>'Reddy','date_of_birth'=>'2019-03-09','permanent_add1'=>'105/1C, Mhada Colony,','relationship_id'=>'4'],
            ['community_id'=>'1','old_family_no'=>'131','old_sal_id'=>'SAL/01/131/01','first_name'=>'Christopher','middle_name'=>null,'last_name'=>'Dsouza','date_of_birth'=>'1977-12-27','permanent_add1'=>'207/2nd Flr Shri Sai Shiv Prerna','relationship_id'=>'1'],
            ['community_id'=>'1','old_family_no'=>'131','old_sal_id'=>'SAL/01/131/02','first_name'=>'Cynthia','middle_name'=>null,'last_name'=>'Dsouza','date_of_birth'=>'1981-04-23','permanent_add1'=>'207/2nd Flr Shri Sai Shiv Prerna','relationship_id'=>'2'],
            ['community_id'=>'1','old_family_no'=>'131','old_sal_id'=>'SAL/01/131/03','first_name'=>'Ethan','middle_name'=>null,'last_name'=>'Dsouza','date_of_birth'=>'2018-02-20','permanent_add1'=>'207/2nd Flr Shri Sai Shiv Prerna','relationship_id'=>'4'],
            // Add more sample data as needed...
        ];

        foreach ($sampleMembers as $member) {
            // Ensure all required fields have default values
            yield array_merge([
                'community_id' => null,
                'community_cluster_id' => null,
                'old_family_no' => '1',
                'old_sal_id' => null,
                'aadhar' => null,
                'birth_family_no' => null,
                'family_no' => null,
                'member_no' => null,
                'registration_year' => null,
                'first_name' => '',
                'middle_name' => null,
                'last_name' => '',
                'date_of_birth' => null,
                'permanent_add1' => null,
                'permanent_add2' => null,
                'permanent_add3' => null,
                'permanent_town_id' => null,
                'permanent_city_id' => null,
                'permanent_state_id' => null,
                'permanent_country_id' => null,
                'permanent_pincode' => null,
                'current_add1' => null,
                'current_add2' => null,
                'current_add3' => null,
                'current_town_id' => null,
                'current_city_id' => null,
                'current_state_id' => null,
                'current_country_id' => null,
                'current_pincode' => null,
                'contact_no_1' => null,
                'contact_no_2' => null,
                'email' => null,
                'blood_group_id' => null,
                'school_name' => null,
                'college_name' => null,
                'latest_qualifications' => null,
                'company_name' => null,
                'income_range_id' => null,
                'baptism_date' => null,
                'baptism_reg_no' => null,
                'baptism_parish' => null,
                'confirmation_date' => null,
                'confirmation_reg_no' => null,
                'confirmation_parish' => null,
                'marriage_date' => null,
                'marriage_reg_no' => null,
                'marriage_parish' => null,
                'death_date' => null,
                'deaths_reg_no' => null,
                'death_parish' => null,
                'family_sequence' => null,
                'member_sequence' => null,
                'marital_status' => null,
                'mother_id' => null,
                'father_id' => null,
                'spouse_id' => null,
                'father_source' => null,
                'mother_source' => null,
                'spouse_source' => null,
                'parish_id' => null,
                'designation_id' => null,
                'gender_id' => null,
                'status_id' => null,
                'relationship_id' => '1',
                'baptism_parish_id' => null,
                'confirmation_parish_id' => null,
                'marriage_parish_id' => null,
                'death_parish_id' => null,
                'created_at' => null,
                'updated_at' => null,
                'deleted_at' => null,
            ], $member);
        }
    }

    /**
     * Process individual member with numbering
     */
    private function processIndividualMember(array $member): array
    {
        $oldFamilyNo = $member['old_family_no'] ?? '1';

        // Initialize family tracking
        if (!isset($this->familyGroups[$oldFamilyNo])) {
            $this->familyGroups[$oldFamilyNo] = $this->familyCounter++;
            $this->familyMemberCounters[$oldFamilyNo] = 1;
        }

        $familyGroup = $this->familyGroups[$oldFamilyNo];
        $familyNo = "SAL-" . str_pad($familyGroup, 3, '0', STR_PAD_LEFT);
        $memberNo = "2025-SAL-M" . str_pad($this->memberCounter, 6, '0', STR_PAD_LEFT);

        // Update member data
        $member['family_no'] = $familyNo;
        $member['member_no'] = $memberNo;
        $member['registration_year'] = '2025';
        $member['family_sequence'] = (int) $familyGroup;
        $member['member_sequence'] = $this->memberCounter;
        $member['created_at'] = now();
        $member['updated_at'] = now();

        $this->familyMemberCounters[$oldFamilyNo]++;
        $this->memberCounter++;

        return $member;
    }

    /**
     * Insert chunk into database
     */
    private function insertChunk(array $chunk): void
    {
        try {
            DB::table('members')->insert($chunk);
        } catch (\Exception $e) {
            $this->command->error("Chunk insert failed: " . $e->getMessage());

            // Try one by one
            foreach ($chunk as $member) {
                try {
                    DB::table('members')->insertOrIgnore([$member]);
                } catch (\Exception $individualError) {
                    $this->command->error("Failed to insert: {$member['first_name']} {$member['last_name']} - " . $individualError->getMessage());
                }
            }
        }
    }

    /**
     * Memory cleanup
     */
    private function cleanup(): void
    {
        // Force garbage collection
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    /**
     * Get current memory usage
     */
    private function getMemoryUsage(): string
    {
        $bytes = memory_get_usage(true);
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}