<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use Modules\Graveyard\Models\ObituaryPage;
use Illuminate\Support\Facades\Log;

class AutoPublishObituaryOnPayment
{
    /**
     * Handle the event.
     */
    public function handle(PaymentCompleted $event): void
    {
        $booking = $event->booking;

        // Check if booking has payment status of 'paid' or 'completed'
        if (!in_array($booking->payment_status, ['paid', 'completed'])) {
            return;
        }

        // Find associated obituary page(s)
        $obituary = null;

        if ($booking instanceof \Modules\Graveyard\Models\PermanentGraveBooking) {
            $obituary = ObituaryPage::where('permanent_grave_booking_id', $booking->id)->first();
        } elseif ($booking instanceof \Modules\Graveyard\Models\TemporaryGraveBooking) {
            $obituary = ObituaryPage::where('temporary_grave_booking_id', $booking->id)->first();
        }

        if (!$obituary) {
            return;
        }

        // Auto-publish obituary if it's not already published and payment is completed
        if (!$obituary->isPublished() && $obituary->canBePublished()) {
            $obituary->publish();

            Log::info("Auto-published obituary page {$obituary->uuid} for {$obituary->deceased_name} after payment completion", [
                'obituary_id' => $obituary->id,
                'booking_type' => get_class($booking),
                'booking_id' => $booking->id,
                'payment_status' => $booking->payment_status
            ]);
        }
    }
}