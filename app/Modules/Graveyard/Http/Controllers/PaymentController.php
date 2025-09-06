<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\Payment;
use Modules\Graveyard\Models\PermanentGraveBooking;
use Modules\Graveyard\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Show payment form for a booking
     */
    public function create(Request $request, string $bookingType, int $bookingId)
    {
        // Validate booking type
        $allowedTypes = ['permanent', 'temporary', 'niche'];
        if (!in_array($bookingType, $allowedTypes)) {
            abort(404, 'Invalid booking type');
        }

        // Get the booking based on type
        $booking = $this->getBookingByType($bookingType, $bookingId);
        if (!$booking) {
            abort(404, 'Booking not found');
        }

        // Check if booking is eligible for payment
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'This booking is not eligible for payment.');
        }

        // Check if booking already has a completed payment
        $hasCompletedPayment = $booking->payments()->where('payment_status', 'completed')->exists();
        if ($hasCompletedPayment) {
            return redirect()->back()->with('error', 'This booking already has a completed payment. No additional payments can be made.');
        }

        // Get existing payment if any
        $existingPayment = $booking->payments()->latest()->first();

        // Get available service types for graveyard
        $serviceTypes = ServiceType::active()->get();

        // Get available payment methods
        $paymentMethods = \Modules\Fund\Models\PaymentMethod::active()->get();

        return Inertia::render('PagesGraveyard/Payment/Create', [
            'booking' => $booking->load(['permanentGrave', 'validMember', 'creator']),
            'bookingType' => $bookingType,
            'existingPayment' => $existingPayment,
            'serviceTypes' => $serviceTypes,
            'paymentMethods' => $paymentMethods,
            'selectedServices' => $booking->selected_services ?? []
        ]);
    }

    /**
     * Store payment for booking
     */
    public function store(Request $request)
    {
        Log::info('Payment store method called');
        Log::info('Payment request data:', $request->all());

        $request->validate([
            'booking_type' => 'required|in:permanent,temporary,niche',
            'booking_id' => 'required|integer',
            'selected_services' => 'required|array|min:1',
            'selected_services.*.service_id' => 'required|exists:service_types,id',
            'selected_services.*.quantity' => 'required|integer|min:1',
            'selected_services.*.unit_cost' => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_mode' => 'required|string|max:50',
            'paid_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string|max:100',
            'payment_notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Get the booking
            $booking = $this->getBookingByType($request->booking_type, $request->booking_id);
            if (!$booking) {
                return back()->withErrors(['error' => 'Booking not found.']);
            }

            // Calculate total amount from selected services
            $totalAmount = 0;
            $serviceCharges = [];

            foreach ($request->selected_services as $serviceData) {
                $service = ServiceType::find($serviceData['service_id']);
                $quantity = (int)$serviceData['quantity'];
                $unitCost = (float)$serviceData['unit_cost'];
                $serviceTotalCost = $quantity * $unitCost;

                $totalAmount += $serviceTotalCost;

                $serviceCharges[] = [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $serviceTotalCost
                ];
            }

            // Create payment record
            $payment = Payment::create([
                'payable_type' => $this->getBookingModelClass($request->booking_type),
                'payable_id' => $booking->id,
                'total_amount' => $totalAmount,
                'paid_amount' => $request->paid_amount,
                'balance_amount' => $totalAmount - $request->paid_amount,
                'payment_method_id' => $request->payment_method_id,
                'payment_mode' => $request->payment_mode,
                'transaction_reference' => $request->transaction_reference,
                'payment_notes' => $request->payment_notes,
                'selected_services' => $request->selected_services,
                'service_charges' => $serviceCharges,
                'payment_date' => $request->payment_date,
                'created_by' => Auth::id() ?: 1,
                'updated_by' => Auth::id() ?: 1,
            ]);

            // Generate receipt if payment is completed
            if ($payment->isCompleted()) {
                $payment->generateReceipt();
            }

            // Update booking's selected services and amounts
            $booking->update([
                'selected_services' => array_column($request->selected_services, 'service_id'),
                'total_cost' => $totalAmount,
                'paid_amount' => $request->paid_amount,
                'balance_amount' => $totalAmount - $request->paid_amount,
                'payment_status' => $payment->payment_status
            ]);

            // If payment is completed, update booking status to confirmed (ready for confirmation)
            if ($payment->payment_status === 'completed') {
                $booking->update([
                    'status' => 'confirmed'  // Payment completed, booking can be confirmed
                ]);
            }

            DB::commit();
            Log::info('Payment created successfully with ID: ' . $payment->id);

            return redirect()->route('graveyard.payments.show', $payment->id)
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create payment: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->withErrors(['error' => 'Failed to record payment. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        $payment->load(['payable', 'payable.ValidMember', 'payable.PermanentGrave', 'paymentMethod', 'creator', 'updater']);

        return Inertia::render('PagesGraveyard/Payment/Show', [
            'payment' => $payment
        ]);
    }

    /**
     * Generate receipt PDF
     */
    public function generateReceipt(Payment $payment)
    {
        if (!$payment->receipt_number) {
            $payment->generateReceipt();
        }

        $payment->load(['payable.permanentGrave', 'payable.validMember', 'paymentMethod', 'creator']);

        return Inertia::render('PagesGraveyard/Payment/Receipt', [
            'payment' => $payment
        ]);
    }

    /**
     * Get booking by type and ID
     */
    private function getBookingByType(string $type, int $id)
    {
        return match ($type) {
            'permanent' => PermanentGraveBooking::find($id),
            'temporary' => null, // TODO: Implement when TemporaryGraveBooking model exists
            'niche' => null, // TODO: Implement when NicheBooking model exists
            default => null
        };
    }

    /**
     * Get booking model class name
     */
    private function getBookingModelClass(string $type): string
    {
        return match ($type) {
            'permanent' => 'Modules\\Graveyard\\Models\\PermanentGraveBooking',
            'temporary' => 'Modules\\Graveyard\\Models\\TemporaryGraveBooking',
            'niche' => 'Modules\\Graveyard\\Models\\NicheBooking',
            default => ''
        };
    }
}
