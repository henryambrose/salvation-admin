<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddAuditToAllModels extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'audit:add-to-models {--dry-run : Show what would be changed without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Add Auditable trait to all models that don\'t already have it';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $modelDirectories = [
            'app/Modules/Members/Models',
            'app/Modules/Fund/Models',
            'app/Modules/Graveyard/Models'
        ];

        $modifiedFiles = 0;
        $skippedFiles = 0;

        foreach ($modelDirectories as $directory) {
            if (!File::exists(base_path($directory))) {
                $this->warn("Directory not found: {$directory}");
                continue;
            }

            $modelFiles = File::files(base_path($directory));

            foreach ($modelFiles as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $filePath = $file->getRealPath();
                $content = File::get($filePath);

                // Skip if already has Auditable trait
                if (str_contains($content, 'use App\Traits\Auditable') ||
                    str_contains($content, ', Auditable') ||
                    str_contains($content, 'Auditable;')) {
                    $skippedFiles++;
                    continue;
                }

                // Skip if it's not a model class
                if (!str_contains($content, 'extends Model') &&
                    !str_contains($content, 'extends Authenticatable')) {
                    $skippedFiles++;
                    continue;
                }

                // Skip the AuditLog model itself
                if (str_contains($content, 'class AuditLog')) {
                    $skippedFiles++;
                    continue;
                }

                $this->info("Processing: {$file->getFilename()}");

                if ($dryRun) {
                    $this->line("  - Would add Auditable trait");
                    $modifiedFiles++;
                    continue;
                }

                // Add the import
                $content = $this->addAuditableImport($content);

                // Add the trait usage
                $content = $this->addAuditableTrait($content);

                File::put($filePath, $content);
                $modifiedFiles++;
                $this->line("  - Added Auditable trait");
            }
        }

        if ($dryRun) {
            $this->info("DRY RUN: Would modify {$modifiedFiles} files, skipped {$skippedFiles} files");
        } else {
            $this->info("Modified {$modifiedFiles} files, skipped {$skippedFiles} files");
        }

        return 0;
    }

    /**
     * Add the Auditable trait import
     */
    private function addAuditableImport(string $content): string
    {
        // Find the namespace line
        if (preg_match('/^namespace\s+[^;]+;$/m', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $namespaceEnd = $matches[0][1] + strlen($matches[0][0]);

            // Insert the import after the namespace
            $import = "\n\nuse App\Traits\Auditable;";
            $content = substr_replace($content, $import, $namespaceEnd, 0);
        }

        return $content;
    }

    /**
     * Add the Auditable trait to the use statement
     */
    private function addAuditableTrait(string $content): string
    {
        // Find existing use statements in the class
        if (preg_match('/use\s+([^;]+);/m', $content, $matches)) {
            $currentTraits = $matches[1];

            // Add Auditable to the existing traits
            $newTraits = trim($currentTraits) . ', Auditable';
            $content = str_replace("use {$currentTraits};", "use {$newTraits};", $content);
        } else {
            // No existing use statement, add one after the class declaration
            if (preg_match('/class\s+\w+[^{]*\{/', $content, $matches, PREG_OFFSET_CAPTURE)) {
                $classStart = $matches[0][1] + strlen($matches[0][0]);
                $traitUsage = "\n    use Auditable;\n";
                $content = substr_replace($content, $traitUsage, $classStart, 0);
            }
        }

        return $content;
    }
}