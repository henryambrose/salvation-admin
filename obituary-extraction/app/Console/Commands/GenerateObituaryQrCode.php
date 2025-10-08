<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Graveyard\Services\ObituaryService;
use Modules\Graveyard\Models\ObituaryPage;

class GenerateObituaryQrCode extends Command
{
    protected $signature = 'obituary:generate-qr 
                            {uuid? : The obituary UUID}
                            {--all : Generate QR codes for all obituaries}
                            {--missing : Generate QR codes only for obituaries without QR codes}
                            {--size=300 : QR code size in pixels}
                            {--printable : Generate high-resolution printable QR codes}';

    protected $description = 'Generate QR codes for obituary pages';

    protected ObituaryService $obituaryService;

    public function __construct(ObituaryService $obituaryService)
    {
        parent::__construct();
        $this->obituaryService = $obituaryService;
    }

    public function handle()
    {
        if ($this->option('all')) {
            return $this->generateForAll();
        }

        if ($this->option('missing')) {
            return $this->generateForMissing();
        }

        $uuid = $this->argument('uuid');
        if (!$uuid) {
            $this->error('Please provide an obituary UUID or use --all or --missing option');
            return Command::FAILURE;
        }

        return $this->generateForSingle($uuid);
    }

    private function generateForSingle(string $uuid): int
    {
        $obituary = ObituaryPage::where('uuid', $uuid)->first();

        if (!$obituary) {
            $this->error("Obituary not found with UUID: {$uuid}");
            return Command::FAILURE;
        }

        try {
            if ($this->option('printable')) {
                $qrUrl = $this->obituaryService->generatePrintableQrCode($obituary);
                $this->info("✅ Printable QR code generated: {$qrUrl}");
            } else {
                $qrUrl = $this->obituaryService->generateQrCode($obituary);
                $this->info("✅ QR code generated: {$qrUrl}");
            }

            $this->line("🔗 Links to: " . route('obituary.show', $obituary->uuid));
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Error generating QR code: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }

    private function generateForAll(): int
    {
        $obituaries = ObituaryPage::all();
        $this->info("Generating QR codes for {$obituaries->count()} obituaries...");

        $success = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($obituaries->count());

        foreach ($obituaries as $obituary) {
            try {
                $this->obituaryService->generateQrCode($obituary);
                $success++;
            } catch (\Exception $e) {
                $this->error("❌ {$obituary->uuid}: {$e->getMessage()}");
                $errors++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Success: {$success}");
        $this->warn("❌ Errors: {$errors}");

        return Command::SUCCESS;
    }

    private function generateForMissing(): int
    {
        $obituaries = ObituaryPage::whereNull('qr_code_path')
            ->orWhere('qr_code_path', '')
            ->get();

        $this->info("Generating QR codes for {$obituaries->count()} obituaries without QR codes...");

        if ($obituaries->isEmpty()) {
            $this->info("✅ All obituaries already have QR codes!");
            return Command::SUCCESS;
        }

        $success = 0;
        $errors = 0;

        foreach ($obituaries as $obituary) {
            try {
                $qrUrl = $this->obituaryService->generateQrCode($obituary);
                $this->info("✅ {$obituary->uuid}: QR generated");
                $success++;
            } catch (\Exception $e) {
                $this->error("❌ {$obituary->uuid}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->info("✅ Success: {$success}");
        $this->warn("❌ Errors: {$errors}");

        return Command::SUCCESS;
    }
}