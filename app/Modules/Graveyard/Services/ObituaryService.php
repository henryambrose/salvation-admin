<?php

namespace Modules\Graveyard\Services;

use Modules\Graveyard\Models\ObituaryPage;
use Modules\Graveyard\Models\ObituaryPayment;
use Modules\Graveyard\Models\PermanentGraveBooking;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ObituaryService
{
    public function createObituaryFromBooking($booking, array $data = []): ObituaryPage
    {
        $serviceType = $data['service_type'] ?? 'basic';

        $obituaryData = [
            'service_type' => $serviceType,
            'biography' => $data['biography'] ?? null,
            'favorite_memory' => $data['favorite_memory'] ?? null,
            'achievements' => $data['achievements'] ?? null,
            'hobbies_interests' => $data['hobbies_interests'] ?? null,
            'notes' => $data['notes'] ?? null,
            'profile_image' => $data['profile_image'] ?? null,
            'gallery_images' => $data['gallery_images'] ?? null,
            'audio_message' => $data['audio_message'] ?? null,
            'allow_condolences' => $data['allow_condolences'] ?? true,
            'allow_memory_sharing' => $data['allow_memory_sharing'] ?? true,
            'is_public' => $data['is_public'] ?? true,
            'theme_color' => $data['theme_color'] ?? '#000000',
            'background_style' => $data['background_style'] ?? 'plain',
            'expires_at' => $this->calculateExpirationDate($serviceType),
            'created_by' => Auth::id(),
        ];

        if ($booking instanceof PermanentGraveBooking) {
            $obituaryData['permanent_grave_booking_id'] = $booking->id;
        } elseif ($booking instanceof TemporaryGraveBooking) {
            $obituaryData['temporary_grave_booking_id'] = $booking->id;
        }

        $obituary = ObituaryPage::create($obituaryData);

        // Generate QR code
        $this->generateQrCode($obituary);

        return $obituary;
    }

    /**
     * Calculate expiration date based on service type
     */
    private function calculateExpirationDate(string $serviceType): Carbon
    {
        $days = config("obituary.duration.{$serviceType}");
        return Carbon::now()->addDays($days);
    }

    public function generateQrCode(ObituaryPage $obituary): string
    {
        $url = route('obituary.show', $obituary->uuid);

        $qrCodeContent = QrCode::format('png')
            ->size(300)
            ->margin(2)
            ->errorCorrection('M')
            ->generate($url);

        $fileName = "qr-codes/obituary-{$obituary->uuid}.png";
        Storage::disk('public')->put($fileName, $qrCodeContent);

        $obituary->update(['qr_code_path' => $fileName]);

        return Storage::url($fileName);
    }

    // public function upgradeToPremiun(ObituaryPage $obituary, float $amount): ObituaryPayment
    // {
    //     $premiumDays = config('obituary.duration.premium');

    //     $payment = ObituaryPayment::create([
    //         'obituary_page_id' => $obituary->id,
    //         'amount' => $amount,
    //         'service_type' => 'premium',
    //         'payment_status' => 'pending',
    //         'expires_at' => Carbon::now()->addDays($premiumDays),
    //     ]);

    //     return $payment;
    // }

    public function processPayment(ObituaryPayment $payment, string $reference): bool
    {
        $payment->update([
            'payment_status' => 'completed',
            'payment_reference' => $reference,
        ]);

        // Upgrade the obituary page to premium
        $payment->obituaryPage->update([
            'service_type' => 'premium',
            'expires_at' => $payment->expires_at,
        ]);

        return true;
    }

    public function createCustomQrCode(ObituaryPage $obituary, array $options = []): string
    {
        $url = route('obituary.show', $obituary->uuid);

        $qrCode = QrCode::format('png')
            ->size($options['size'] ?? 300)
            ->margin($options['margin'] ?? 2)
            ->errorCorrection($options['error_correction'] ?? 'M');

        if (isset($options['color'])) {
            $qrCode->color($options['color']['r'], $options['color']['g'], $options['color']['b']);
        }

        if (isset($options['background_color'])) {
            $qrCode->backgroundColor(
                $options['background_color']['r'],
                $options['background_color']['g'],
                $options['background_color']['b']
            );
        }

        $qrCodeContent = $qrCode->generate($url);

        $fileName = "qr-codes/custom-obituary-{$obituary->uuid}-" . time() . ".png";
        Storage::disk('public')->put($fileName, $qrCodeContent);

        // Update the obituary to point to the new custom QR code
        $obituary->update(['qr_code_path' => $fileName]);

        return Storage::url($fileName);
    }

    public function getObituaryStats(ObituaryPage $obituary): array
    {
        return [
            'total_views' => $obituary->view_count,
            'qr_scans' => $obituary->qr_scan_count,
            'condolences_count' => $obituary->condolences()->count(),
            'approved_condolences_count' => $obituary->approvedCondolences()->count(),
            'pending_condolences_count' => $obituary->condolences()->where('is_approved', false)->count(),
            'gallery_images_count' => count($obituary->gallery_images ?? []),
            'created_days_ago' => $obituary->created_at->diffInDays(now()),
        ];
    }

    public function trackView(ObituaryPage $obituary, bool $fromQrCode = false): void
    {
        $obituary->incrementViewCount();

        if ($fromQrCode) {
            $obituary->incrementQrScanCount();
        }
    }

    public function getRecommendedUpgrades(ObituaryPage $obituary): array
    {
        $recommendations = [];

        if ($obituary->service_type === 'basic') {
            $recommendations[] = [
                'feature' => 'Premium Service',
                'description' => 'Unlimited photos, custom themes, audio messages',
                'price' => 500.00, // Configure this in your app config
            ];
        }

        if (!$obituary->gallery_images || count($obituary->gallery_images) < 3) {
            $recommendations[] = [
                'feature' => 'Photo Gallery',
                'description' => 'Add more cherished memories with multiple photos',
                'action' => 'upload_photos',
            ];
        }

        if (!$obituary->audio_message) {
            $recommendations[] = [
                'feature' => 'Audio Message',
                'description' => 'Add a personal voice message from family',
                'action' => 'add_audio',
            ];
        }

        return $recommendations;
    }

    public function generatePrintableQrCode(ObituaryPage $obituary): string
    {
        $url = route('obituary.show', $obituary->uuid);

        // Generate high-resolution QR code for printing
        $qrCodeContent = QrCode::format('png')
            ->size(600) // Higher resolution for printing
            ->margin(4)
            ->errorCorrection('H') // High error correction for durability
            ->generate($url);

        $fileName = "qr-codes/printable-obituary-{$obituary->uuid}.png";
        Storage::disk('public')->put($fileName, $qrCodeContent);

        return Storage::url($fileName);
    }

    public function cleanupExpiredObitaries(): int
    {
        $expiredCount = 0;
        $gracePeriodDays = config('obituary.grace_period_days', 30);
        $graceCutoff = Carbon::now()->subDays($gracePeriodDays);

        // Handle expired obituaries
        $expiredObitaries = ObituaryPage::where('expires_at', '<', now())
            ->get();

        foreach ($expiredObitaries as $obituary) {
            // If beyond grace period, deactivate completely
            if ($obituary->expires_at < $graceCutoff) {
                $obituary->update([
                    'is_active' => false,
                    'is_public' => false,
                ]);
            }
            // Within grace period for premium - downgrade to basic with new expiration
            elseif ($obituary->service_type === 'premium') {
                $basicDays = config('obituary.duration.basic');
                $obituary->update([
                    'service_type' => 'basic',
                    'expires_at' => Carbon::now()->addDays($basicDays),
                ]);
            }
            // Basic service expired - deactivate after grace period
            else {
                $obituary->update([
                    'is_active' => false,
                ]);
            }

            $expiredCount++;
        }

        return $expiredCount;
    }

    public function generateShareableLink(ObituaryPage $obituary): array
    {
        $url = route('obituary.show', $obituary->uuid);

        return [
            'url' => $url,
            'qr_code' => $obituary->qr_code_path ? Storage::url($obituary->qr_code_path) : null,
            'social_media' => [
                'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($url),
                'twitter' => "https://twitter.com/intent/tweet?url=" . urlencode($url) . "&text=" . urlencode("Remembering " . $obituary->deceased_name),
                'whatsapp' => "https://wa.me/?text=" . urlencode("Remembering " . $obituary->deceased_name . " - " . $url),
            ],
        ];
    }
}
