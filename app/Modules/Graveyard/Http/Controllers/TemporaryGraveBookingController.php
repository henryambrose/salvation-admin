<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use Modules\Graveyard\Models\TemporaryGrave;
use Modules\Graveyard\Models\ServiceType;
use Modules\Members\Models\Gender;
use Modules\Members\Models\Parish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TemporaryGraveBookingController extends Controller
{
    /**
     * Display temporary grave bookings
     */
    public function index(Request $request)
    {
        $query = TemporaryGraveBooking::with([
            'temporaryGrave',
            'gender',
            'parish',
            'applicantMember',
            'creator'
        ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->withStatus($request->status);
        }

        if ($request->filled('search')) {
            $query->searchDeceased($request->search);
        }

        if ($request->filled('transfer_due')) {
            if ($request->transfer_due === 'due_soon') {
                $query->where('expected_transfer_date', '<=', now()->addMonths(2));
            } elseif ($request->transfer_due === 'overdue') {
                $query->where('expected_transfer_date', '<', now());
            }
        }

        // Pagination
        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('PagesGraveyard/TemporaryGraveBooking/Index', [
            'bookings' => $bookings,
            'filters' => $request->only(['status', 'search', 'transfer_due'])
        ]);
    }

    /**
     * Show temporary grave booking form
     */
    public function create()
    {
        return Inertia::render('PagesGraveyard/TemporaryGraveBooking/Create', [
            'serviceTypes' => ServiceType::active()->byCategory('grave')->get(),
            'availableGraves' => TemporaryGrave::where('is_available', true)->get(),
            'genders' => Gender::all(),
            'parishes' => Parish::all()
        ]);
    }

    /**
     * Store temporary grave booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'temporary_grave_id' => 'required|exists:temporary_graves,id',
            'dead_first_name' => 'required|string|max:100',
            'dead_last_name' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date|before:died_on',
            'age' => 'nullable|integer|min:0|max:150',
            'months' => 'nullable|integer|min:0|max:11',
            'days' => 'nullable|integer|min:0|max:30',
            'died_on' => 'required|date|before_or_equal:today',
            'buried_on' => 'required|date|after_or_equal:died_on',
            'gender_id' => 'required|exists:genders,id',
            'cause_of_death' => 'required|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'parish_id' => 'nullable|exists:parishes,id',
            'minister' => 'nullable|string|max:255',
            'applicant_type' => 'required|in:member,non_member',
            'applicant_name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'contact_email' => 'nullable|email',
            'relationship_to_deceased' => 'nullable|string|max:100',
            'permit_no' => 'nullable|string|max:50',
            'selected_services' => 'nullable|array',
            'selected_services.*' => 'exists:service_types,id',
            'duration_months' => 'nullable|integer|min:6|max:24'
        ]);

        try {
            DB::beginTransaction();

            // Check if temporary grave is still available
            $grave = TemporaryGrave::findOrFail($request->temporary_grave_id);
            if (!$grave->is_available) {
                return back()->withErrors(['grave' => 'This temporary grave is no longer available.']);
            }

            // Create the booking
            $booking = TemporaryGraveBooking::create([
                'temporary_grave_id' => $request->temporary_grave_id,
                'dead_first_name' => $request->dead_first_name,
                'dead_last_name' => $request->dead_last_name,
                'date_of_birth' => $request->date_of_birth,
                'age' => $request->age,
                'months' => $request->months,
                'days' => $request->days,
                'died_on' => $request->died_on,
                'buried_on' => $request->buried_on,
                'gender_id' => $request->gender_id,
                'cause_of_death' => $request->cause_of_death,
                'nationality' => $request->nationality,
                'parish_id' => $request->parish_id,
                'minister' => $request->minister,
                'applicant_type' => $request->applicant_type,
                'applicant_name' => $request->applicant_name,
                'contact_no' => $request->contact_no,
                'contact_email' => $request->contact_email,
                'relationship_to_deceased' => $request->relationship_to_deceased,
                'permit_no' => $request->permit_no,
                'selected_services' => $request->selected_services,
                'duration_months' => $request->duration_months ?? 12,
                'special_requirements' => $request->special_requirements,
                'status' => 'pending',
                'payment_status' => 'pending',
                'created_by' => auth()->id,
                'updated_by' => auth()->id,
            ]);

            // Calculate total cost from selected services
            if ($request->selected_services) {
                $totalCost = ServiceType::whereIn('id', $request->selected_services)->sum('cost');
                $booking->update([
                    'total_cost' => $totalCost,
                    'balance_amount' => $totalCost
                ]);
            }

            DB::commit();

            return redirect()->route('graveyard.temporary-grave-bookings.show', $booking->id)
                ->with('success', 'Temporary grave booking created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed to create booking. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show booking details
     */
    public function show(TemporaryGraveBooking $temporaryGraveBooking)
    {
        $temporaryGraveBooking->load([
            'temporaryGrave',
            'gender',
            'parish',
            'applicantMember',
            'creator',
            'updater',
            'nicheTransfers'
        ]);

        return Inertia::render('PagesGraveyard/TemporaryGraveBooking/Show', [
            'booking' => $temporaryGraveBooking
        ]);
    }

    /**
     * Confirm a booking
     */
    public function confirm(TemporaryGraveBooking $temporaryGraveBooking)
    {
        if ($temporaryGraveBooking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be confirmed.');
        }

        $temporaryGraveBooking->update(['status' => 'confirmed']);

        return back()->with('success', 'Booking confirmed successfully.');
    }

    /**
     * Cancel a booking
     */
    public function cancel(Request $request, TemporaryGraveBooking $temporaryGraveBooking)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $temporaryGraveBooking->update([
                'status' => 'cancelled',
                'remarks' => $request->cancellation_reason,
                'updated_by' => auth()->id
            ]);

            // Free up the temporary grave if it was occupied
            if ($temporaryGraveBooking->temporaryGrave && !$temporaryGraveBooking->temporaryGrave->is_available) {
                $temporaryGraveBooking->temporaryGrave->update([
                    'is_available' => true,
                    'occupied_date' => null,
                    'updated_by' => auth()->id
                ]);
            }

            DB::commit();

            return back()->with('success', 'Booking cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel booking.');
        }
    }

    /**
     * Request transfer to niche for this booking
     */
    public function requestTransfer(TemporaryGraveBooking $temporaryGraveBooking)
    {
        if ($temporaryGraveBooking->status !== 'confirmed') {
            return back()->with('error', 'Only confirmed bookings can request transfers.');
        }

        if ($temporaryGraveBooking->transfer_requested) {
            return back()->with('error', 'Transfer has already been requested for this booking.');
        }

        if ($temporaryGraveBooking->requestTransfer()) {
            return redirect()->route('graveyard.niche-transfers.create', ['booking_id' => $temporaryGraveBooking->id])
                ->with('success', 'Transfer request initiated. Please complete the transfer form.');
        }

        return back()->with('error', 'Failed to initiate transfer request.');
    }

    /**
     * Get bookings eligible for transfer (API endpoint)
     */
    public function eligibleForTransfer(Request $request)
    {
        $bookings = TemporaryGraveBooking::eligibleForTransfer()
            ->with(['temporaryGrave'])
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'deceased_full_name' => $booking->deceased_full_name,
                    'grave_no' => $booking->temporaryGrave->grave_no,
                    'buried_on' => $booking->buried_on,
                    'expected_transfer_date' => $booking->expected_transfer_date,
                    'is_overdue' => $booking->isTransferOverdue()
                ];
            });

        return response()->json($bookings);
    }
}
