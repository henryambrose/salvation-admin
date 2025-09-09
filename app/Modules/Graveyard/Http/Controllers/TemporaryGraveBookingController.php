<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use Modules\Graveyard\Models\TemporaryGrave;
use Modules\Graveyard\Models\ServiceType;
use Modules\Members\Models\Gender;
use Modules\Members\Models\Parish;
use Modules\Members\Models\Relationship;
use Modules\Members\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
                $monthsFromEnv = (int) config('app.graveyard_min_months_before_niche_transfer', 6);
                $query->where('expected_transfer_date', '<=', now()->addMonths($monthsFromEnv));
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
            'availableGraves' => TemporaryGrave::available()->get(),
            'genders' => Gender::all(),
            'parishes' => Parish::all(),
            'relationships' => Relationship::all()
        ]);
    }

    /**
     * Search members for deceased person selection
     */
    public function searchMembers(Request $request)
    {
        $query = $request->get('query', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $members = Member::with('community')
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'LIKE', '%' . $query . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $query . '%')
                    ->orWhere('member_no', 'LIKE', '%' . $query . '%')
                    ->orWhere('family_no', 'LIKE', '%' . $query . '%')
                    ->orWhere('contact_no_1', 'LIKE', '%' . $query . '%')
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $query . '%']);
            })
            ->whereNotNull('first_name')
            ->whereNotNull('last_name')
            ->orderBy('first_name')
            ->limit(20)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->id,
                    'name' => $member->first_name . ' ' . $member->last_name,
                    'full_name' => $member->first_name . ' ' . $member->last_name,
                    'member_no' => $member->member_no,
                    'family_no' => $member->family_no,
                    'community' => $member->community ? [
                        'name' => $member->community->name
                    ] : null,
                    'current_add1' => $member->current_add1,
                    'contact_no_1' => $member->contact_no_1,
                    'gender' => $member->gender,
                ];
            });

        return response()->json($members);
    }

    /**
     * Store temporary grave booking
     */
    public function store(Request $request)
    {
        Log::info('Creating temporary grave booking', $request->all());
        $request->validate([
            'temporary_grave_id' => 'required|exists:temporary_graves,id',
            'deceased_person_type' => 'required|in:member,external',
            'deceased_member_id' => 'required_if:deceased_person_type,member|exists:members,id',
            'dead_first_name' => 'required_if:deceased_person_type,external|string|max:100',
            'dead_last_name' => 'required_if:deceased_person_type,external|string|max:100',
            'date_of_birth' => 'nullable|date|before:died_on',
            'age' => 'nullable|integer|min:0|max:150',
            'months' => 'nullable|integer|min:0|max:11',
            'days' => 'nullable|integer|min:0|max:30',
            'died_on' => 'required|date|before_or_equal:today',
            'buried_on' => 'required|date|after_or_equal:died_on',
            'gender_id' => 'required_if:deceased_person_type,external|nullable|exists:genders,id',
            'cause_of_death' => 'required|string|max:255',
            'nationality' => 'nullable|string|max:100',
            'parish_id' => 'nullable|exists:parishes,id',
            'minister' => 'nullable|string|max:255',
            'applicant_type' => 'required|in:member,external',
            'applicant_name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'contact_email' => 'nullable|email',
            'relationship_id' => 'nullable|exists:relationships,id',
            'permit_no' => 'nullable|string|max:50',
            'selected_services' => 'nullable|array',
            'selected_services.*' => 'exists:service_types,id',
        ]);

        try {
            DB::beginTransaction();

            // Check if temporary grave is still available
            $grave = TemporaryGrave::findOrFail($request->temporary_grave_id);
            if (!$grave->status == 'available') {
                return back()->with('error', 'This temporary grave is no longer available.');
            }

            // Handle member selection vs manual entry
            $deadFirstName = $request->dead_first_name;
            $deadLastName = $request->dead_last_name;
            $genderId = $request->gender_id;

            if ($request->deceased_person_type === 'member' && $request->deceased_member_id) {
                $member = Member::findOrFail($request->deceased_member_id);
                $deadFirstName = $member->first_name;
                $deadLastName = $member->last_name;
                // Try to find gender ID based on member gender
                $gender = Gender::where('name', $member->gender)->first();
                $genderId = $gender ? $gender->id : null;
            }

            // Create the booking
            $booking = TemporaryGraveBooking::create([
                'temporary_grave_id' => $request->temporary_grave_id,
                'deceased_member_id' => $request->deceased_member_id,
                'dead_first_name' => $deadFirstName,
                'dead_last_name' => $deadLastName,
                'date_of_birth' => $request->date_of_birth,
                'age' => $request->age,
                'months' => $request->months,
                'days' => $request->days,
                'died_on' => $request->died_on,
                'buried_on' => $request->buried_on,
                'gender_id' => $genderId,
                'cause_of_death' => $request->cause_of_death,
                'nationality' => $request->nationality,
                'parish_id' => $request->parish_id,
                'minister' => $request->minister,
                'applicant_type' => $request->applicant_type,
                'applicant_name' => $request->applicant_name,
                'contact_no' => $request->contact_no,
                'contact_email' => $request->contact_email,
                'relationship_id' => $request->relationship_id,
                'permit_no' => $request->permit_no,
                'selected_services' => $request->selected_services,
                'special_requirements' => $request->special_requirements,
                'status' => 'pending',
                'payment_status' => 'pending',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            Log::info('Temporary grave booking created', ['booking_id' => $booking->id]);
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
            Log::error('Error creating temporary grave booking', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to create booking. Please try again.')
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
            'nicheTransfers',
            'payments'
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
                'updated_by' => Auth::id()
            ]);

            // Free up the temporary grave if it was occupied
            if ($temporaryGraveBooking->temporaryGrave && $temporaryGraveBooking->temporaryGrave->status === 'unavailable') {
                $temporaryGraveBooking->temporaryGrave->release();
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

        // Validate minimum time elapsed since death before niche transfer
        $minMonthsBeforeTransfer = (int) config('app.graveyard_min_months_before_niche_transfer', 6);
        $diedOn = \Carbon\Carbon::parse($temporaryGraveBooking->died_on);
        $eligibleDate = $diedOn->addMonths($minMonthsBeforeTransfer);

        if (now()->lt($eligibleDate)) {
            $formattedEligibleDate = $eligibleDate->format('d M Y');
            $monthsWord = $minMonthsBeforeTransfer === 1 ? 'month' : 'months';
            return back()->with(
                'error',
                "Transfer to niche is not yet eligible. Minimum {$minMonthsBeforeTransfer} {$monthsWord} must pass after death. " .
                    "This booking will be eligible for transfer on {$formattedEligibleDate}."
            );
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
