<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\Payment;
use Modules\Graveyard\Models\PermanentGraveBooking;
use Modules\Graveyard\Models\TemporaryGraveBooking;
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
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['payable', 'paymentMethod', 'creator'])
            ->latest();

        // Filter by payment status if provided
        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        // Filter by booking type if provided
        if ($request->filled('booking_type')) {
            switch ($request->booking_type) {
                case 'permanent':
                    $query->where('payable_type', 'Modules\\Graveyard\\Models\\PermanentGraveBooking');
                    break;
                case 'temporary':
                    $query->where('payable_type', 'Modules\\Graveyard\\Models\\TemporaryGraveBooking');
                    break;
                case 'niche':
                    $query->where('payable_type', 'Modules\\Graveyard\\Models\\NicheBooking');
                    break;
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                    ->orWhere('transaction_reference', 'like', "%{$search}%")
                    ->orWhereHas('payable', function ($subQuery) use ($search) {
                        $subQuery->where('booking_reference', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->paginate(15)->withQueryString();
        return Inertia::render('PagesGraveyard/Payment/Index', [
            'payments' => $payments,
            'filters' => $request->only(['status', 'booking_type', 'search']),
        ]);
    }

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

        // Load appropriate relationships based on booking type
        $relationshipsToLoad = match ($bookingType) {
            'permanent' => ['permanentGrave', 'validMember', 'creator'],
            'temporary' => ['temporaryGrave', 'creator'],
            'niche' => ['niche', 'creator'],
            default => ['creator']
        };

        return Inertia::render('PagesGraveyard/Payment/Create', [
            'booking' => $booking->load($relationshipsToLoad),
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

        $request->validate([
            'booking_type' => 'required|in:permanent,temporary,niche',
            'booking_id' => 'required|integer',
            'selected_services' => 'required|array|min:1',
            'selected_services.*.service_id' => 'required|exists:service_types,id',
            'selected_services.*.quantity' => 'required|integer|min:1',
            'selected_services.*.unit_cost' => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'paid_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string|max:100',
            'payment_notes' => 'nullable|string|max:1000',
            'concession_amount' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Get the booking
            $booking = $this->getBookingByType($request->booking_type, $request->booking_id);
            if (!$booking) {
                return back()->withErrors(['error' => 'Booking not found.']);
            }

            // Calculate total amount from selected services
            $serviceSubtotal = 0;
            $serviceCharges = [];

            foreach ($request->selected_services as $serviceData) {
                $service = ServiceType::find($serviceData['service_id']);
                $quantity = (int)$serviceData['quantity'];
                $unitCost = (float)$serviceData['unit_cost'];
                $serviceTotalCost = $quantity * $unitCost;

                $serviceSubtotal += $serviceTotalCost;

                $serviceCharges[] = [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $serviceTotalCost
                ];
            }

            // Apply concession discount
            $concessionAmount = (float)($request->concession_amount ?? 0);
            $totalAmount = max(0, $serviceSubtotal - $concessionAmount);

            // Create payment record
            $payment = Payment::create([
                'payable_type' => $this->getBookingModelClass($request->booking_type),
                'payable_id' => $booking->id,
                'total_amount' => $totalAmount,
                'paid_amount' => $request->paid_amount,
                'balance_amount' => $totalAmount - $request->paid_amount,
                'concession_amount' => $concessionAmount,
                'payment_method_id' => $request->payment_method_id,
                'transaction_reference' => $request->transaction_reference,
                'payment_notes' => $request->payment_notes,
                'selected_services' => $request->selected_services,
                'service_charges' => $serviceCharges,
                'payment_date' => $request->payment_date,
                'created_by' => Auth::id() ?: 1,
                'updated_by' => Auth::id() ?: 1,
            ]);

            // Generate receipt for all payments (including partial payments)
            $payment->generateReceipt();

            // Update booking's selected services and amounts
            $booking->update([
                'selected_services' => array_column($request->selected_services, 'service_id'),
                'total_cost' => $totalAmount,
                'paid_amount' => $request->paid_amount,
                'balance_amount' => $totalAmount - $request->paid_amount,
                'payment_status' => $payment->payment_status
            ]);

            // Confirm booking on ANY payment (partial or full) - burial cannot be delayed
            if ($payment->paid_amount > 0) {
                $booking->update([
                    'status' => 'confirmed'  // Any payment received, booking confirmed for burial
                ]);
            }

            DB::commit();

            // Redirect back to the appropriate booking page
            $redirectRoute = $this->getBookingShowRoute($request->booking_type);
            return redirect()->route($redirectRoute, $booking->id)
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
     * Show balance payment form
     */
    public function balancePaymentForm(Payment $payment)
    {
        // Check if payment is eligible for balance payment
        if ($payment->payment_status !== 'partial' || $payment->balance_amount <= 0) {
            return redirect()->back()->with('error', 'This payment is not eligible for balance payment.');
        }

        $payment->load(['payable.permanentGrave', 'payable.validMember', 'paymentMethod', 'creator']);

        // Get available payment methods
        $paymentMethods = \Modules\Fund\Models\PaymentMethod::active()->get();

        return Inertia::render('PagesGraveyard/Payment/Balance', [
            'originalPayment' => $payment,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Store balance payment
     */
    public function storeBalancePayment(Request $request, Payment $payment)
    {
        // Refresh payment data to get latest status
        $payment->refresh();
        // Check if original payment is eligible for balance payment
        if ($payment->payment_status !== 'partial' || $payment->balance_amount <= 0) {
            return back()->withErrors(['error' => 'This payment is not eligible for balance payment.']);
        }

        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'paid_amount' => 'required|numeric|min:0.01|max:' . $payment->balance_amount,
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string|max:100',
            'payment_notes' => 'nullable|string|max:1000'
        ]);


        try {
            DB::beginTransaction();

            // Create balance payment record
            $balancePayment = Payment::create([
                'payable_type' => $payment->payable_type,
                'payable_id' => $payment->payable_id,
                'total_amount' => $request->paid_amount, // Balance payment total = amount paid
                'paid_amount' => $request->paid_amount,
                'balance_amount' => 0, // Balance payment has no remaining balance
                'concession_amount' => 0, // No additional concessions on balance payments
                'payment_method_id' => $request->payment_method_id,
                'transaction_reference' => $request->transaction_reference,
                'payment_notes' => $request->payment_notes,
                'selected_services' => [], // No services for balance payment
                'service_charges' => [], // No service charges for balance payment
                'payment_date' => $request->payment_date,
                'created_by' => Auth::id() ?: 1,
                'updated_by' => Auth::id() ?: 1,
            ]);
            // Update original payment's balance
            $newBalance = $payment->balance_amount - $request->paid_amount;
            $newPaidAmount = $payment->paid_amount + $request->paid_amount;

            $payment->update([
                'balance_amount' => $newBalance,
                'paid_amount' => $newPaidAmount
            ]);

            // Generate receipt for balance payment (whether partial or complete)
            $balancePayment->generateReceipt();

            // Update booking - it should already be confirmed from initial payment
            $booking = $payment->payable;
            if ($newBalance <= 0) {
                $booking->update([
                    'payment_status' => 'completed',
                    'status' => 'confirmed', // Ensure confirmed status (should already be confirmed)
                    'paid_amount' => $newPaidAmount,
                    'balance_amount' => $newBalance
                ]);
            } else {
                $booking->update([
                    'payment_status' => 'partial',
                    'status' => 'confirmed', // Ensure confirmed status (should already be confirmed)
                    'paid_amount' => $newPaidAmount,
                    'balance_amount' => $newBalance
                ]);
            }

            DB::commit();

            return redirect()->route('graveyard.payments.show', $balancePayment->id)
                ->with('success', 'Balance payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create balance payment: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->withErrors(['error' => 'Failed to record balance payment. Please try again.'])
                ->withInput();
        }
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
            'temporary' => TemporaryGraveBooking::find($id),
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

    /**
     * Get booking show route name by type
     */
    private function getBookingShowRoute(string $type): string
    {
        return match ($type) {
            'permanent' => 'graveyard.permanent-grave-bookings.show',
            'temporary' => 'graveyard.temporary-grave-bookings.show',
            'niche' => 'graveyard.niche-transfers.show', // or appropriate niche route
            default => 'graveyard.dashboard'
        };
    }
}
