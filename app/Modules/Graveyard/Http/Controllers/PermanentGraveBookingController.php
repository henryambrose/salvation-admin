<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\PermanentGraveBooking;
use Modules\Graveyard\Models\PermanentGrave;
use Modules\Graveyard\Models\ValidMember;
use Modules\Graveyard\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class PermanentGraveBookingController extends Controller
{
    /**
     * Display permanent grave bookings
     */
    public function index(Request $request)
    {
        $query = PermanentGraveBooking::with([
            'permanentGrave',
            'validMember.member',
            'creator'
        ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->withStatus($request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('permit_no', 'like', "%{$search}%")
                ->orWhere('booking_reference', 'like', "%{$search}%")
                ->orWhere('applicant_name', 'like', "%{$search}%")
                ->orWhereHas('permanentGrave', function ($q) use ($search) {
                    $q->where('owner_name', 'like', "%{$search}%");
                })
                ->orWhereHas('validMember', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                });
        }

        // Pagination
        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('PagesGraveyard/PermanentGraveBooking/Index', [
            'bookings' => $bookings,
            'filters' => $request->only(['status', 'search'])
        ]);
    }

    /**
     * Show permanent grave booking form
     */
    public function create()
    {
        return Inertia::render('PagesGraveyard/PermanentGraveBooking/Create', [
            'serviceTypes' => ServiceType::active()->byCategory('grave')->get()
        ]);
    }

    /**
     * Search permanent graves by owner name or grave number
     */
    public function searchPermanentGrave(Request $request)
    {
        Log::info('Search Term: ' . $request->search_term . ', Search Type: ' . $request->search_type);
        $request->validate([
            'search_term' => 'required|string|min:2',
        ]);

        $query = PermanentGrave::with(['validMembers']);

        $query->where('owner_name', 'like', '%' . $request->search_term . '%')
            ->orWhere('grave_no', 'like', '%' . $request->search_term . '%');

        Log::info('Query Built: ' . $query->toSql());
        $graves = $query->get()->map(function (PermanentGrave $grave) {
            return [
                'id' => $grave->id,
                'grave_no' => $grave->grave_no,
                'owner_name' => $grave->owner_name,
                'section' => $grave->section,
                'row_no' => $grave->row_no,
                'last_burial_date' => $grave->last_burial_date,
                'is_eligible' => $this->checkGraveEligibility($grave),
                'eligibility_message' => $this->getEligibilityMessage($grave),
                'valid_members' => $grave->validMembers->map(function (ValidMember $member) {
                    return [
                        'id' => $member->id,
                        'full_name' => $member->full_name,
                        'relationship' => $member->relationship,
                        'is_deceased' => !is_null($member->death_date)
                    ];
                }),
                'has_valid_members' => $grave->validMembers->count() > 0,
                'available_members_count' => $grave->validMembers->whereNull('death_date')->values()
            ];
        });
        Log::info('Found Graves: ' . $graves->count());
        return response()->json([
            'graves' => $graves,
            'found' => $graves->count()
        ]);
    }

    /**
     * Store permanent grave booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'permanent_grave_id' => 'required|exists:permanent_graves,id',
            'valid_member_id' => 'required|exists:valid_members,id',
            'died_on' => 'required|date|before_or_equal:today',
            'buried_on' => 'required|date|after_or_equal:died_on',
            'cause_of_death' => 'required|string|max:255',
            'minister' => 'nullable|string|max:255',
            'applicant_type' => 'required|in:member,non_member',
            'applicant_name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'contact_email' => 'nullable|email',
            'permit_no' => 'nullable|string|max:50',
            'selected_services' => 'nullable|array',
            'selected_services.*' => 'exists:service_types,id'
        ]);

        try {
            DB::beginTransaction();

            // Get the permanent grave
            $grave = PermanentGrave::findOrFail($request->permanent_grave_id);

            // Double-check eligibility
            if (!$this->checkGraveEligibility($grave)) {
                return back()->withErrors(['grave' => 'This grave is not eligible for burial yet.']);
            }

            // Get the valid member
            $validMember = ValidMember::findOrFail($request->valid_member_id);

            // Check if valid member is already deceased
            if ($validMember->death_date) {
                return back()->withErrors(['valid_member' => 'This valid member is already marked as deceased.']);
            }

            // Create the booking
            $booking = PermanentGraveBooking::create([
                'permanent_grave_id' => $request->permanent_grave_id,
                'valid_member_id' => $request->valid_member_id,
                'died_on' => $request->died_on,
                'buried_on' => $request->buried_on,
                'cause_of_death' => $request->cause_of_death,
                'minister' => $request->minister,
                'applicant_type' => $request->applicant_type,
                'applicant_name' => $request->applicant_name,
                'contact_no' => $request->contact_no,
                'contact_email' => $request->contact_email,
                'permit_no' => $request->permit_no,
                'selected_services' => $request->selected_services,
                'special_requirements' => $request->special_requirements,
                'status' => 'pending',
                'payment_status' => 'pending',
                'created_by' => auth()->id ?? null,
                'updated_by' => auth()->id ?? null,
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

            return redirect()->route('graveyard.permanent-grave-bookings.show', $booking->id)
                ->with('success', 'Permanent grave booking created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Failed to create booking. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Show booking details
     */
    public function show(PermanentGraveBooking $permanentGraveBooking)
    {
        $permanentGraveBooking->load([
            'permanentGrave',
            'validMember.member',
            'creator',
            'updater'
        ]);

        return Inertia::render('PagesGraveyard/PermanentGraveBooking/Show', [
            'booking' => $permanentGraveBooking
        ]);
    }

    /**
     * Confirm a booking
     */
    public function confirm(PermanentGraveBooking $permanentGraveBooking)
    {
        if (!$permanentGraveBooking->canBeConfirmed()) {
            return back()->with('error', 'This booking cannot be confirmed at this time.');
        }

        if ($permanentGraveBooking->confirm()) {
            return back()->with('success', 'Booking confirmed successfully.');
        }

        return back()->with('error', 'Failed to confirm booking.');
    }

    /**
     * Cancel a booking
     */
    public function cancel(Request $request, PermanentGraveBooking $permanentGraveBooking)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        if ($permanentGraveBooking->cancel($request->cancellation_reason)) {
            return back()->with('success', 'Booking cancelled successfully.');
        }

        return back()->with('error', 'Failed to cancel booking.');
    }

    /**
     * Check if grave is eligible for new burial (24-month rule)
     */
    private function checkGraveEligibility($grave): bool
    {
        if (!$grave->last_burial_date) {
            return true; // Never used, eligible
        }

        $lastBurial = Carbon::parse($grave->last_burial_date);
        $monthsSinceLastBurial = $lastBurial->diffInMonths(now());

        return $monthsSinceLastBurial >= 24;
    }

    /**
     * Get eligibility message for grave
     */
    private function getEligibilityMessage($grave): string
    {
        if (!$grave->last_burial_date) {
            return 'This grave has never been used and is eligible for burial.';
        }

        $lastBurial = Carbon::parse($grave->last_burial_date);
        $monthsSinceLastBurial = $lastBurial->diffInMonths(now());

        if ($monthsSinceLastBurial >= 24) {
            return "This grave is eligible for burial. Last burial was {$monthsSinceLastBurial} months ago.";
        } else {
            $remainingMonths = 24 - $monthsSinceLastBurial;
            return "This grave cannot be used for burial yet. {$remainingMonths} months remaining until eligible.";
        }
    }
}
