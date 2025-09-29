<?php

/**
 * Script to extract member data from the original seeder file
 * and convert it to a more memory-efficient format
 */

require_once __DIR__ . '/../vendor/autoload.php';

class MemberDataExtractor
{
    private $sourceFile;
    private $outputDir;

    public function __construct()
    {
        $this->sourceFile = __DIR__ . '/../database/modules/members/database/seeders/MemberSeeder.php';
        $this->outputDir = __DIR__ . '/../storage/app/member_data/';

        // Ensure output directory exists
        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }
    }

    public function extractToCSV(): void
    {
        echo "Extracting member data to CSV format...\n";

        // Read the source file in chunks to avoid memory issues
        $handle = fopen($this->sourceFile, 'r');
        if (!$handle) {
            throw new \Exception("Cannot open source file: {$this->sourceFile}");
        }

        $csvFile = $this->outputDir . 'members.csv';
        $csvHandle = fopen($csvFile, 'w');

        // Write CSV header
        $headers = [
            'community_id', 'community_cluster_id', 'old_family_no', 'old_sal_id', 'aadhar',
            'birth_family_no', 'family_no', 'member_no', 'registration_year', 'first_name',
            'middle_name', 'last_name', 'date_of_birth', 'permanent_add1', 'permanent_add2',
            'permanent_add3', 'permanent_town_id', 'permanent_city_id', 'permanent_state_id',
            'permanent_country_id', 'permanent_pincode', 'current_add1', 'current_add2',
            'current_add3', 'current_town_id', 'current_city_id', 'current_state_id',
            'current_country_id', 'current_pincode', 'contact_no_1', 'contact_no_2', 'email',
            'blood_group_id', 'school_name', 'college_name', 'latest_qualifications',
            'company_name', 'income_range_id', 'baptism_date', 'baptism_reg_no',
            'baptism_parish', 'confirmation_date', 'confirmation_reg_no', 'confirmation_parish',
            'marriage_date', 'marriage_reg_no', 'marriage_parish', 'death_date',
            'deaths_reg_no', 'death_parish', 'family_sequence', 'member_sequence',
            'marital_status', 'mother_id', 'father_id', 'spouse_id', 'father_source',
            'mother_source', 'spouse_source', 'parish_id', 'designation_id', 'gender_id',
            'status_id', 'relationship_id', 'baptism_parish_id', 'confirmation_parish_id',
            'marriage_parish_id', 'death_parish_id'
        ];

        fputcsv($csvHandle, $headers);

        $memberCount = 0;
        $inArraySection = false;
        $currentMember = '';
        $arrayDepth = 0;

        while (($line = fgets($handle)) !== false) {
            $trimmed = trim($line);

            // Detect start of member array
            if (strpos($trimmed, '$members = [') !== false) {
                $inArraySection = true;
                continue;
            }

            // Detect end of member array
            if ($inArraySection && $trimmed === '];') {
                $inArraySection = false;
                break;
            }

            if ($inArraySection) {
                // Track array depth
                $arrayDepth += substr_count($line, '[') - substr_count($line, ']');

                $currentMember .= $line;

                // When we complete a member record (array depth returns to 0)
                if ($arrayDepth === 0 && !empty(trim($currentMember))) {
                    $this->processMemberLine($currentMember, $csvHandle);
                    $memberCount++;

                    if ($memberCount % 100 === 0) {
                        echo "Processed $memberCount members...\n";
                    }

                    $currentMember = '';
                }
            }
        }

        fclose($handle);
        fclose($csvHandle);

        echo "Extraction complete! Processed $memberCount members.\n";
        echo "CSV file created: $csvFile\n";
    }

    private function processMemberLine(string $memberData, $csvHandle): void
    {
        try {
            // Clean up the member data line
            $memberData = trim($memberData, " \t\n\r\0\x0B[],");

            // Parse the PHP array format
            $memberArray = $this->parseArrayString($memberData);

            if ($memberArray) {
                // Convert to CSV row
                $csvRow = $this->arrayToCsvRow($memberArray);
                fputcsv($csvHandle, $csvRow);
            }

        } catch (\Exception $e) {
            echo "Error processing member: " . $e->getMessage() . "\n";
        }
    }

    private function parseArrayString(string $arrayString): ?array
    {
        // Simple parser for the PHP array format
        $result = [];

        // Split by comma and process key-value pairs
        $pairs = explode(',', $arrayString);

        foreach ($pairs as $pair) {
            if (strpos($pair, '=>') !== false) {
                [$key, $value] = explode('=>', $pair, 2);

                $key = trim($key, " \t\n\r\0\x0B'\"");
                $value = trim($value, " \t\n\r\0\x0B'\"");

                if ($value === 'null') {
                    $value = null;
                }

                $result[$key] = $value;
            }
        }

        return empty($result) ? null : $result;
    }

    private function arrayToCsvRow(array $memberArray): array
    {
        $headers = [
            'community_id', 'community_cluster_id', 'old_family_no', 'old_sal_id', 'aadhar',
            'birth_family_no', 'family_no', 'member_no', 'registration_year', 'first_name',
            'middle_name', 'last_name', 'date_of_birth', 'permanent_add1', 'permanent_add2',
            'permanent_add3', 'permanent_town_id', 'permanent_city_id', 'permanent_state_id',
            'permanent_country_id', 'permanent_pincode', 'current_add1', 'current_add2',
            'current_add3', 'current_town_id', 'current_city_id', 'current_state_id',
            'current_country_id', 'current_pincode', 'contact_no_1', 'contact_no_2', 'email',
            'blood_group_id', 'school_name', 'college_name', 'latest_qualifications',
            'company_name', 'income_range_id', 'baptism_date', 'baptism_reg_no',
            'baptism_parish', 'confirmation_date', 'confirmation_reg_no', 'confirmation_parish',
            'marriage_date', 'marriage_reg_no', 'marriage_parish', 'death_date',
            'deaths_reg_no', 'death_parish', 'family_sequence', 'member_sequence',
            'marital_status', 'mother_id', 'father_id', 'spouse_id', 'father_source',
            'mother_source', 'spouse_source', 'parish_id', 'designation_id', 'gender_id',
            'status_id', 'relationship_id', 'baptism_parish_id', 'confirmation_parish_id',
            'marriage_parish_id', 'death_parish_id'
        ];

        $row = [];
        foreach ($headers as $header) {
            $row[] = $memberArray[$header] ?? null;
        }

        return $row;
    }

    public function createMinimalSeeder(): void
    {
        echo "Creating minimal seeder that uses CSV...\n";

        $seederContent = '<?php

namespace Modules\Members\Database\Seeders;

use Modules\Members\Models\Member;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberSeederFromCSV extends Seeder
{
    private $memberCounter = 1;
    private $familyCounter = 1;
    private $familyGroups = [];

    public function run(): void
    {
        $this->command->info(\'Starting CSV-based member seeding...\');

        ini_set(\'memory_limit\', \'256M\');
        DB::statement(\'SET FOREIGN_KEY_CHECKS=0\');

        try {
            $this->processCSVFile();
        } finally {
            DB::statement(\'SET FOREIGN_KEY_CHECKS=1\');
        }

        $this->command->info(\'Member seeding completed!\');
    }

    private function processCSVFile(): void
    {
        $csvFile = storage_path(\'app/member_data/members.csv\');

        if (!file_exists($csvFile)) {
            $this->command->error(\'CSV file not found: \' . $csvFile);
            return;
        }

        $handle = fopen($csvFile, \'r\');
        $headers = fgetcsv($handle); // Skip header row

        $batch = [];
        $batchSize = 50;
        $processed = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $member = array_combine($headers, $row);
            $processedMember = $this->processIndividualMember($member);
            $batch[] = $processedMember;

            if (count($batch) >= $batchSize) {
                DB::table(\'members\')->insertOrIgnore($batch);
                $processed += count($batch);
                $this->command->line("Processed: $processed members");

                $batch = [];
                gc_collect_cycles();
            }
        }

        // Insert remaining members
        if (!empty($batch)) {
            DB::table(\'members\')->insertOrIgnore($batch);
            $processed += count($batch);
        }

        fclose($handle);
        $this->command->info("Total processed: $processed members");
    }

    private function processIndividualMember(array $member): array
    {
        $oldFamilyNo = $member[\'old_family_no\'] ?? \'1\';

        if (!isset($this->familyGroups[$oldFamilyNo])) {
            $this->familyGroups[$oldFamilyNo] = $this->familyCounter++;
        }

        $familyGroup = $this->familyGroups[$oldFamilyNo];
        $familyNo = "SAL-" . str_pad($familyGroup, 3, \'0\', STR_PAD_LEFT);
        $memberNo = "2025-SAL-M" . str_pad($this->memberCounter, 6, \'0\', STR_PAD_LEFT);

        $member[\'family_no\'] = $familyNo;
        $member[\'member_no\'] = $memberNo;
        $member[\'registration_year\'] = \'2025\';
        $member[\'family_sequence\'] = (int) $familyGroup;
        $member[\'member_sequence\'] = $this->memberCounter;
        $member[\'created_at\'] = now();
        $member[\'updated_at\'] = now();

        // Convert null strings to actual null
        foreach ($member as $key => $value) {
            if ($value === \'null\' || $value === \'\') {
                $member[$key] = null;
            }
        }

        $this->memberCounter++;
        return $member;
    }
}';

        file_put_contents(__DIR__ . '/../database/modules/members/database/seeders/MemberSeederFromCSV.php', $seederContent);
        echo "CSV-based seeder created!\n";
    }
}

// Run the extraction
try {
    $extractor = new MemberDataExtractor();
    $extractor->extractToCSV();
    $extractor->createMinimalSeeder();
    echo "All done! You can now use MemberSeederFromCSV for memory-efficient seeding.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}