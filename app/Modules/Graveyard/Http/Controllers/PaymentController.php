<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\Payment;
use Modules\Graveyard\Models\PermanentGraveBooking;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use Modules\Graveyard\Models\NicheTransfer;
use Modules\Graveyard\Models\ServiceType;
use Modules\Graveyard\Models\PermanentGrave;
use Modules\Graveyard\Models\Niche;
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
        $this->authorize('list-payment');
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
                case 'niche-transfer':
                    $query->where('payable_type', 'Modules\\Graveyard\\Models\\NicheTransfer');
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

        // Get payment status counts (across all pages, not just current page)
        $statusCounts = [
            'partial' => Payment::where('payment_status', 'partial')->count(),
            'completed' => Payment::where('payment_status', 'completed')->count(),
            'refunded' => Payment::where('payment_status', 'refunded')->count(),
        ];

        // Get pending bookings (bookings awaiting payment)
        $pendingBookings = collect();

        // Temporary Grave Bookings with pending payments
        $pendingTemporary = TemporaryGraveBooking::with(['temporaryGrave', 'gender'])
            ->where('payment_status', 'pending')
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'type' => 'temporary',
                    'type_label' => 'Temporary Grave',
                    'reference' => $booking->booking_reference ?? 'TGB-' . $booking->id,
                    'deceased_name' => trim($booking->dead_first_name . ' ' . $booking->dead_last_name),
                    'grave_info' => $booking->temporaryGrave ? 'Grave: ' . $booking->temporaryGrave->grave_no : 'N/A',
                    'total_cost' => $booking->total_cost ?? 0,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at,
                    'payment_url' => route('graveyard.payments.create', ['bookingType' => 'temporary', 'bookingId' => $booking->id]),
                ];
            });

        // Permanent Grave Bookings with pending payments
        $pendingPermanent = PermanentGraveBooking::with(['permanentGrave', 'validMember'])
            ->where('payment_status', 'pending')
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'type' => 'permanent',
                    'type_label' => 'Permanent Grave',
                    'reference' => $booking->booking_reference ?? 'PGB-' . $booking->id,
                    'deceased_name' => $booking->validMember ? trim($booking->validMember->first_name . ' ' . $booking->validMember->last_name) : $booking->applicant_name,
                    'grave_info' => $booking->permanentGrave ? 'Grave: ' . $booking->permanentGrave->grave_no : 'N/A',
                    'total_cost' => $booking->total_cost ?? 0,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at,
                    'payment_url' => route('graveyard.payments.create', ['bookingType' => 'permanent', 'bookingId' => $booking->id]),
                ];
            });

        // Merge all pending bookings
        $pendingBookings = $pendingTemporary->merge($pendingPermanent)->sortByDesc('created_at');

        return Inertia::render('PagesGraveyard/Payment/Index', [
            'payments' => $payments,
            'pendingBookings' => $pendingBookings->values(),
            'statusCounts' => $statusCounts,
            'filters' => $request->only(['status', 'booking_type', 'search']),
        ]);
    }

    /**
     * Show payment form for a booking
     */
    public function create(Request $request, string $bookingType, int $bookingId)
    {
        $this->authorize('create-payment');
        // Validate booking type
        $allowedTypes = ['permanent', 'temporary', 'niche', 'niche-transfer'];
        if (!in_array($bookingType, $allowedTypes)) {
            abort(404, 'Invalid booking type');
        }

        // Get the booking based on type
        $booking = $this->getBookingByType($bookingType, $bookingId);
        if (!$booking) {
            abort(404, 'Booking not found');
        }

        // Check if booking is eligible for payment
        if ($bookingType === 'niche-transfer') {
            if (!in_array($booking->status, ['pending', 'approved'])) {
                return redirect()->back()->with('error', 'This transfer is not eligible for payment.');
            }
        } else {
            if (!in_array($booking->status, ['pending', 'confirmed'])) {
                return redirect()->back()->with('error', 'This booking is not eligible for payment.');
            }
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
        $this->authorize('create-payment');
        // Check if any selected services are free
        $hasFreeServices = false;
        if (!empty($request->selected_services)) {
            $selectedServiceIds = collect($request->selected_services)->pluck('service_id');
            $freeServicesCount = ServiceType::whereIn('id', $selectedServiceIds)
                ->where('type', 'free')
                ->count();
            $hasFreeServices = $freeServicesCount > 0;
        }

        $validationRules = [
            'booking_type' => 'required|in:permanent,temporary,niche-transfer',
            'booking_id' => 'required|integer',
            'selected_services' => 'nullable|array',
            'selected_services.*.service_id' => 'required_with:selected_services|exists:service_types,id',
            'selected_services.*.quantity' => 'required_with:selected_services|integer|min:1',
            'selected_services.*.unit_cost' => 'required_with:selected_services|numeric|min:0',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string|max:100',
            'payment_notes' => 'nullable|string|max:1000',
            'concession_amount' => 'nullable|numeric|min:0'
        ];

        // For free services, payment method and amount are not required
        if ($hasFreeServices) {
            $validationRules['payment_method_id'] = 'nullable|exists:payment_methods,id';
            $validationRules['paid_amount'] = 'nullable|numeric|min:0';
        } else {
            $validationRules['payment_method_id'] = 'required|exists:payment_methods,id';
            $validationRules['paid_amount'] = 'required|numeric|min:0.01';
        }

        $request->validate($validationRules);

        try {
            DB::beginTransaction();

            // Get the booking
            $booking = $this->getBookingByType($request->booking_type, $request->booking_id);
            if (!$booking) {
                return back()->withErrors(['error' => 'Booking not found.']);
            }

            // Calculate total amount from selected services or use for balance payment
            $serviceSubtotal = 0;
            $serviceCharges = [];
            $isBalancePayment = empty($request->selected_services);

            // Process selected services if any
            if (!$isBalancePayment) {
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
            }

            // Apply concession discount (only for service payments)
            $concessionAmount = $isBalancePayment ? 0 : (float)($request->concession_amount ?? 0);

            // For balance payments, total amount is the paid amount (no services)
            // For service payments, calculate from services minus concession
            $totalAmount = $isBalancePayment
                ? (float)$request->paid_amount
                : max(0, $serviceSubtotal - $concessionAmount);

            // For free services, ensure amounts are set correctly
            $paidAmount = $hasFreeServices ? 0 : (float)$request->paid_amount;
            if ($hasFreeServices) {
                $totalAmount = 0;
                $paidAmount = 0;
            }

            // Validate payment amount doesn't exceed total amount (skip for free services)
            if (!$hasFreeServices && $paidAmount > $totalAmount) {
                return back()->withErrors([
                    'paid_amount' => 'Payment amount cannot exceed the total amount of ' . number_format($totalAmount, 2)
                ])->withInput();
            }

            // For balance payments, also validate against booking's remaining balance
            if ($isBalancePayment && $paidAmount > $booking->balance_amount) {
                return back()->withErrors([
                    'paid_amount' => 'Payment amount cannot exceed the outstanding balance of ' . number_format($booking->balance_amount, 2)
                ])->withInput();
            }

            // Create payment record
            $payment = Payment::create([
                'payable_type' => $this->getBookingModelClass($request->booking_type),
                'payable_id' => $booking->id,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'balance_amount' => max(0, $totalAmount - $paidAmount),
                'concession_amount' => $concessionAmount,
                'payment_method_id' => $hasFreeServices ? null : $request->payment_method_id,
                'transaction_reference' => $request->transaction_reference,
                'payment_notes' => $hasFreeServices ?
                    ($request->payment_notes ? $request->payment_notes . ' (Free Service)' : 'Free Service - No Payment Required') :
                    $request->payment_notes,
                'selected_services' => $request->selected_services,
                'service_charges' => $serviceCharges,
                'payment_date' => $request->payment_date,
                'created_by' => Auth::id() ?: 1,
                'updated_by' => Auth::id() ?: 1,
            ]);

            // Generate receipt for all payments (including partial payments)
            $payment->generateReceipt();

            // Update booking amounts
            if ($isBalancePayment) {
                // For balance payments, update payment amounts but keep existing services and total cost
                $newPaidAmount = $booking->paid_amount + $request->paid_amount;
                $newBalanceAmount = max(0, $booking->total_cost - $newPaidAmount);

                $booking->update([
                    'paid_amount' => $newPaidAmount,
                    'balance_amount' => $newBalanceAmount,
                    'payment_status' => $payment->payment_status
                ]);
            } else {
                // For service payments, update services and all amounts
                $booking->update([
                    'selected_services' => array_column($request->selected_services, 'service_id'),
                    'total_cost' => $totalAmount,
                    'paid_amount' => $paidAmount,
                    'balance_amount' => max(0, $totalAmount - $paidAmount),
                    'payment_status' => $payment->payment_status
                ]);
            }

            // Confirm booking on ANY payment (partial or full) OR free services - burial cannot be delayed
            if ($payment->paid_amount > 0 || $hasFreeServices) {
                $booking->update([
                    'status' => 'confirmed'  // Any payment received OR free services confirmed, booking confirmed for burial
                ]);
            }

            // Update member death record if the deceased person is a member
            $member = null;
            $deathDate = null;
            $burialDate = null;

            if ($request->booking_type === 'permanent' && $booking->validMember && $booking->validMember->member) {
                $member = $booking->validMember->member;
                $deathDate = $booking->dead_date ?? now()->toDateString();
                $burialDate = $booking->dead_date ?? now()->toDateString();
            } elseif ($request->booking_type === 'temporary' && $booking->deceasedMember) {
                $member = $booking->deceasedMember;
                $deathDate = $booking->died_on ?? now()->toDateString();
                $burialDate = $booking->died_on ?? now()->toDateString();
            }

            if ($member && !$member->deathrecord_id) {
                // Create death record first
                $deathRecord = \Modules\Members\Models\DeathRecord::create([
                    'member_id' => $member->id,
                    'death_date' => $deathDate,
                    'burial_date' => $burialDate,
                    'deceased_name' => $member->first_name,
                    'deceased_surname' => $member->last_name,
                ]);

                // Then update member with death record ID
                $member->update([
                    'deathrecord_id' => $deathRecord->id,
                    'status_id' => 4
                ]);
            }

            DB::commit();

            // Redirect back to the appropriate booking page
            $redirectRoute = $this->getBookingShowRoute($request->booking_type);
            return redirect()->route($redirectRoute, $booking->id)
                ->with('success', 'Payment recorded successfully.')
                ->with('receipt_id', $payment->id)
                ->with('receipt_url', route('graveyard.payments.receipt', $payment->id));
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
        $this->authorize('read-payment');
        $payment->load($this->getPaymentRelationships($payment));

        return Inertia::render('PagesGraveyard/Payment/Show', [
            'payment' => $payment
        ]);
    }

    /**
     * Show balance payment form
     */
    public function balancePaymentForm(Payment $payment)
    {
        $this->authorize('create-payment');
        // Check if payment is eligible for balance payment
        if ($payment->payment_status !== 'partial' || $payment->balance_amount <= 0) {
            return redirect()->back()->with('error', 'This payment is not eligible for balance payment.');
        }

        $payment->load($this->getPaymentRelationships($payment));

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
        $this->authorize('create-payment');
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
                ->with('success', 'Balance payment recorded successfully.')
                ->with('receipt_id', $balancePayment->id)
                ->with('receipt_url', route('graveyard.payments.receipt', $balancePayment->id));
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
        $this->authorize('read-payment');
        if (!$payment->receipt_number) {
            $payment->generateReceipt();
        }

        $payment->load($this->getPaymentRelationships($payment));

        // Get deceased name
        $deceasedName = null;
        if ($payment->payable) {
            $validMember = $payment->payable->validMember ?? null;
            if ($validMember) {
                $deceasedName = trim(($validMember->first_name ?? '') . ' ' . ($validMember->last_name ?? ''));
            } else {
                $deceasedName = $payment->payable->applicant_name ?? null;
            }
        }

        // Get grave number
        $graveNumber = null;
        if ($payment->payable && $payment->payable->permanentGrave) {
            $graveNumber = $payment->payable->permanentGrave->grave_no;
        }

        // Prepare receipt data
        $receiptData = [
            'receipt_number' => $payment->receipt_number,
            'payment_reference' => $payment->payment_reference,
            'payment_date' => $payment->payment_date ? date('d/m/Y', strtotime($payment->payment_date)) : date('d/m/Y'),
            'booking_reference' => $payment->payable->booking_reference ?? null,
            'deceased_name' => $deceasedName,
            'grave_number' => $graveNumber,
            'applicant_name' => $payment->payable->applicant_name ?? null,
            'service_charges' => $payment->service_charges ?? [],
            'total_amount' => $payment->total_amount,
            'paid_amount' => $payment->paid_amount,
            'amount_words' => $this->numberToWords($payment->paid_amount),
            'balance_amount' => $payment->balance_amount,
            'payment_method' => $payment->paymentMethod->name ?? $payment->payment_method->name ?? 'N/A',
            'payment_mode' => $payment->payment_mode,
            'transaction_reference' => $payment->transaction_reference,
            'payment_status' => $payment->payment_status,
            'payment_notes' => $payment->payment_notes,
            'recorded_by' => $payment->creator->name ?? 'N/A',
            'created_at' => $payment->created_at->format('d/m/Y H:i A'),
        ];

        return view('graveyard.receipts.payment', $receiptData);
    }

    /**
     * Get booking by type and ID
     */
    private function getBookingByType(string $type, int $id)
    {
        return match ($type) {
            'permanent' => PermanentGraveBooking::find($id),
            'temporary' => TemporaryGraveBooking::find($id),
            'niche-transfer' => NicheTransfer::find($id),
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
            'niche-transfer' => 'Modules\\Graveyard\\Models\\NicheTransfer',
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
            'niche-transfer' => 'graveyard.niche-transfers.show', // or appropriate niche route
            default => 'graveyard.dashboard'
        };
    }

    /**
     * Get payment relationships based on payable type
     */
    private function getPaymentRelationships(Payment $payment): array
    {
        $baseRelationships = ['paymentMethod', 'creator', 'updater'];

        // Determine payable type from payment
        if (str_contains($payment->payable_type, 'PermanentGraveBooking')) {
            return array_merge($baseRelationships, ['payable.permanentGrave', 'payable.validMember']);
        } elseif (str_contains($payment->payable_type, 'TemporaryGraveBooking')) {
            return array_merge($baseRelationships, ['payable.temporaryGrave', 'payable.gender', 'payable.parish']);
        } elseif (str_contains($payment->payable_type, 'NicheBooking')) {
            return array_merge($baseRelationships, ['payable.niche']);
        }

        return array_merge($baseRelationships, ['payable']);
    }

    /**
     * Show maintenance fee payment form for a permanent grave
     */
    public function createMaintenancePayment(Request $request, int $graveId)
    {
        $this->authorize('create-payment');
        $grave = \Modules\Graveyard\Models\PermanentGrave::with(['member', 'maintenancePayments'])
            ->findOrFail($graveId);

        // Calculate pending amount
        $pendingAmount = $grave->calculatePendingAmount();

        if ($pendingAmount <= 0) {
            return redirect()->route('graveyard.permanent-graves.index')
                ->with('error', 'This grave has no pending maintenance fees.');
        }

        // Get available payment methods
        $paymentMethods = \Modules\Fund\Models\PaymentMethod::active()->get();

        // Get payment history for this grave
        $paymentHistory = $grave->maintenancePayments()
            ->with(['paymentMethod', 'creator'])
            ->latest()
            ->get();

        $annualFee = config('graveyard.annual_maintenance_fee', 5000);
        $monthlyFee = $annualFee / 12;
        $currentYear = now()->year;

        // Calculate available months for partial payment
        $paidMonths = $grave->partial_payment_months[$currentYear] ?? [];
        $availableMonths = [];

        for ($month = 1; $month <= 12; $month++) {
            if (!in_array($month, $paidMonths)) {
                $availableMonths[] = [
                    'value' => $month,
                    'label' => date('F', mktime(0, 0, 0, $month, 1)),
                    'amount' => $monthlyFee
                ];
            }
        }

        return Inertia::render('PagesGraveyard/Payment/CreateMaintenance', [
            'grave' => $grave,
            'pendingAmount' => $pendingAmount,
            'paymentMethods' => $paymentMethods,
            'paymentHistory' => $paymentHistory,
            'annualFee' => $annualFee,
            'monthlyFee' => $monthlyFee,
            'availableMonths' => $availableMonths,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * Store maintenance fee payment
     */
    public function storeMaintenancePayment(Request $request)
    {
        $this->authorize('create-payment');
        $request->validate([
            'payable_type' => 'required|string|in:permanent_grave,niche',
            'payable_id' => 'required|integer',
            'payer_name' => 'required|string|max:255',
            'payer_phone' => 'required|string|max:20',
            'payer_email' => 'nullable|email|max:255',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'payment_date' => 'required|date',
            'months_paying_for' => 'nullable|array',
            'months_paying_for.*' => 'integer|min:1|max:12',
            'transaction_reference' => 'nullable|string|max:100',
            'payment_notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Get the payable model (PermanentGrave or Niche)
            if ($request->payable_type === 'permanent_grave') {
                $payable = PermanentGrave::findOrFail($request->payable_id);
                $payableClass = 'Modules\\Graveyard\\Models\\PermanentGrave';
                $payableLabel = 'Permanent Grave';
            } else {
                $payable = Niche::findOrFail($request->payable_id);
                $payableClass = 'Modules\\Graveyard\\Models\\Niche';
                $payableLabel = 'Niche';
            }

            // Prepare payment notes with payer information
            $paymentNotes = "Annual Maintenance Fee Payment ({$payableLabel})";
            $paymentNotes .= "\nPaid by: {$request->payer_name}";
            $paymentNotes .= "\nPhone: {$request->payer_phone}";
            if ($request->payer_email) {
                $paymentNotes .= "\nEmail: {$request->payer_email}";
            }
            if ($request->payment_notes) {
                $paymentNotes .= "\nNotes: {$request->payment_notes}";
            }

            // Calculate total pending amount and balance after payment using historical rates
            $totalPendingAmount = $payable->calculatePendingAmount();
            $paidAmount = $request->payment_amount;

            // Round up the balance amount for partial payments
            $balanceAmount = $totalPendingAmount - $paidAmount;
            if ($balanceAmount > 0) {
                $balanceAmount = ceil($balanceAmount);
            }

            // Create payment record
            $payment = Payment::create([
                'payable_type' => $payableClass,
                'payable_id' => $payable->id,
                'total_amount' => $totalPendingAmount,
                'paid_amount' => $paidAmount,
                'balance_amount' => max(0, $balanceAmount),
                'payment_status' => $balanceAmount <= 0 ? 'completed' : 'partial',
                'payment_method_id' => $request->payment_method_id,
                'transaction_reference' => $request->transaction_reference,
                'payment_notes' => $paymentNotes,
                'payment_date' => $request->payment_date,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Generate receipt with historical rate information
            $payment->generateReceipt();

            // Record maintenance payment in grave/niche (uses historical rates internally)
            $monthsFor = $request->months_paying_for ?? [];
            $payable->recordMaintenancePayment($request->payment_amount, $monthsFor);

            DB::commit();

            return redirect()->route('graveyard.payments.show', $payment->id)
                ->with('success', 'Maintenance fee payment recorded successfully.')
                ->with('receipt_id', $payment->id)
                ->with('receipt_url', route('graveyard.payments.receipt', $payment->id));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create maintenance payment: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return back()->withErrors(['error' => 'Failed to record maintenance payment. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Convert number to words for receipt
     */
    private function numberToWords($number)
    {
        $amount = number_format($number, 2, '.', '');
        list($rupees, $paise) = explode('.', $amount);

        $words = '';
        if ($rupees > 0) {
            $words = $this->convertNumberToWords((int)$rupees) . ' Rupees';
        }
        if ($paise > 0) {
            $words .= ($words ? ' and ' : '') . $this->convertNumberToWords((int)$paise) . ' Paise';
        }

        return $words ?: 'Zero Rupees';
    }

    /**
     * Helper function to convert number to words
     */
    private function convertNumberToWords($number)
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $teens = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

        if ($number < 10) {
            return $ones[$number];
        } elseif ($number < 20) {
            return $teens[$number - 10];
        } elseif ($number < 100) {
            return $tens[intval($number / 10)] . ' ' . $ones[$number % 10];
        } elseif ($number < 1000) {
            return $ones[intval($number / 100)] . ' Hundred ' . $this->convertNumberToWords($number % 100);
        } elseif ($number < 100000) {
            return $this->convertNumberToWords(intval($number / 1000)) . ' Thousand ' . $this->convertNumberToWords($number % 1000);
        } elseif ($number < 10000000) {
            return $this->convertNumberToWords(intval($number / 100000)) . ' Lakh ' . $this->convertNumberToWords($number % 100000);
        } else {
            return $this->convertNumberToWords(intval($number / 10000000)) . ' Crore ' . $this->convertNumberToWords($number % 10000000);
        }
    }
}
