<?php

namespace Modules\Members\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessLargeSeeder extends Command
{
    protected $signature = 'seeder:process-large {file : The seeder file to process} {--chunk=100 : Number of records per chunk}';

    protected $description = 'Process large seeder files in chunks to avoid memory issues';

    public function handle()
    {
        $file = $this->argument('file');
        $chunkSize = $this->option('chunk');

        if (! file_exists($file)) {
            $this->error("File {$file} not found!");

            return 1;
        }

        $this->info("Processing {$file} in chunks of {$chunkSize}...");

        // Read file line by line to extract data
        $data = $this->extractDataFromFile($file);

        if (empty($data)) {
            $this->error('No data found in file!');

            return 1;
        }

        $this->info('Found '.count($data).' records to process.');

        // Process in chunks
        $chunks = array_chunk($data, $chunkSize);
        $bar = $this->output->createProgressBar(count($chunks));

        foreach ($chunks as $chunk) {
            try {
                DB::table('members')->insert($chunk);
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nError inserting chunk: ".$e->getMessage());

                return 1;
            }
        }

        $bar->finish();
        $this->newLine();
        $this->info('✅ Successfully processed all records!');

        return 0;
    }

    private function extractDataFromFile(string $file): array
    {
        $data = [];
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Look for array lines that contain member data
            if (strpos($line, '[\'community_id\'=>') !== false) {
                // Extract the array data
                $arrayData = $this->parseArrayLine($line);
                if ($arrayData) {
                    $data[] = $arrayData;
                }
            }
        }

        return $data;
    }

    private function parseArrayLine(string $line): ?array
    {
        // Remove the array wrapper and parse the data
        $line = trim($line);
        if (strpos($line, '[') === 0) {
            $line = substr($line, 1);
        }
        if (strrpos($line, ']') === strlen($line) - 1) {
            $line = substr($line, 0, -1);
        }

        // Parse the key-value pairs
        $pairs = explode(',', $line);
        $data = [];

        foreach ($pairs as $pair) {
            $pair = trim($pair);
            if (strpos($pair, '=>') !== false) {
                [$key, $value] = explode('=>', $pair, 2);
                $key = trim($key, "'");
                $value = trim($value, "'");

                // Handle null values
                if ($value === 'null') {
                    $value = null;
                }

                $data[$key] = $value;
            }
        }

        return $data;
    }
}
