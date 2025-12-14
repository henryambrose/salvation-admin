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
use Illuminate\Support\Facades\Auth;

class PermanentGraveBookingController extends Controller
{
    /**
     * Display permanent grave bookings
     */
    public function index(Request $request)
    {
        $this->authorize('list-permanent-grave-booking');

        $query = PermanentGraveBooking::with([
            'permanentGrave',
            'validMember.member',
            'creator',
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
                })
                ->orWhereHas('permanentGrave', function ($q) use ($search) {
                    $q->whereRaw("CONCAT(block, '-', `row`, '-', `column`) LIKE ?", ["%{$search}%"]);
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
        $request->validate([
            'search_term' => 'required|string|min:2',
        ]);

        $query = PermanentGrave::with(['validMembers']);

        $query->where('owner_name', 'like', '%' . $request->search_term . '%')
            ->orWhere('old_no', 'like', '%' . $request->search_term . '%')
            ->orWhere('contact_no', 'like', '%' . $request->search_term . '%');

        $graves = $query->get()->map(function (PermanentGrave $grave) {
            $pendingMaintenanceFee = $grave->calculatePendingAmount();

            return [
                'id' => $grave->id,
                'column' => $grave->column,
                'owner_name' => $grave->owner_name,
                'block' => $grave->block,
                'row' => $grave->row,
                'last_burial_date' => $grave->last_burial_date,
                'is_eligible' => $this->checkGraveEligibility($grave),
                'eligibility_message' => $this->getEligibilityMessage($grave),
                'pending_maintenance_fee' => $pendingMaintenanceFee,
                'has_pending_maintenance' => $pendingMaintenanceFee > 0,
                'valid_members' => $grave->validMembers->map(function (ValidMember $member) {
                    return [
                        'id' => $member->id,
                        'full_name' => $member->full_name,
                        'first_name' => $member->first_name,
                        'last_name' => $member->last_name,
                        'relationship' => $member->relationship,
                        'member_type' => $member->member_type,
                        'is_deceased' => $member->is_deceased,
                        'death_date' => $member->death_date?->format('Y-m-d'),
                        'burial_date' => $member->burial_date?->format('Y-m-d')
                    ];
                }),
                'has_valid_members' => $grave->validMembers->count() > 0,
                'available_members_count' => $grave->validMembers->whereNull('death_date')->values()
            ];
        });
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
        $this->authorize('create-permanent-grave-booking');

        try {
            $request->validate([
                'permanent_grave_id' => 'required|exists:permanent_graves,id',
                'valid_member_id' => 'required|exists:valid_members,id',
                'died_on' => 'required|date',
                'buried_on' => 'required|date|after_or_equal:died_on',
                'cause_of_death' => 'required|string|max:255',
                'minister' => 'nullable|string|max:255',
                'applicant_type' => 'required|in:member,external',
                'applicant_name' => 'required|string|max:255',
                'contact_no' => 'required|string|max:20',
                'contact_email' => 'nullable|email',
                'permit_no' => 'nullable|string|max:50',
                'selected_services' => 'nullable|array',
                'selected_services.*' => 'exists:service_types,id',
                'special_requirements' => 'nullable|string|max:1000'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ', $e->errors());
            throw $e;
        }

        try {
            DB::beginTransaction();

            // Get the permanent grave
            $grave = PermanentGrave::findOrFail($request->permanent_grave_id);

            // Double-check eligibility
            if (!$this->checkGraveEligibility($grave)) {
                Log::warning('Grave not eligible for burial');
                return back()->withErrors(['permanent_grave_id' => 'This grave is not eligible for burial yet.']);
            }

            // Log pending maintenance fees for information (not blocking burial)
            $pendingMaintenanceFee = $grave->calculatePendingAmount();
            if ($pendingMaintenanceFee > 0) {
                Log::info('Grave has pending maintenance fees (tracked separately)', [
                    'grave_id' => $grave->id,
                    'pending_amount' => $pendingMaintenanceFee
                ]);
            }

            // Check for existing active bookings for this grave
            $existingBooking = PermanentGraveBooking::where('permanent_grave_id', $request->permanent_grave_id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->exists();

            if ($existingBooking) {
                Log::warning('Grave already has an active booking');
                return back()->withErrors(['permanent_grave_id' => 'This grave already has a pending or confirmed booking. Cannot create duplicate booking.']);
            }

            // Get the valid member
            $validMember = ValidMember::findOrFail($request->valid_member_id);

            // Check if valid member is already deceased
            if ($validMember->death_date) {
                Log::warning('Valid member already deceased');
                return back()->withErrors(['valid_member_id' => 'This valid member is already marked as deceased.']);
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
                'created_by' => Auth::id() ?: 1, // Default to user ID 1 if not authenticated
                'updated_by' => Auth::id() ?: 1, // Default to user ID 1 if not authenticated
            ]);

            // Calculate total cost from selected services only
            if ($request->selected_services) {
                $totalCost = ServiceType::whereIn('id', $request->selected_services)->sum('cost');
                $booking->update([
                    'total_cost' => $totalCost,
                    'balance_amount' => $totalCost
                ]);
            }

            DB::commit();

            return redirect()->route('graveyard.permanent-grave-bookings.index')
                ->with('success', 'Permanent grave booking created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create permanent grave booking: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

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
            'validMember.gender',
            'validMember.parish',
            'validMember.relationship',
            'creator',
            'updater',
            'payments.paymentMethod',
         ]);

        return Inertia::render('PagesGraveyard/PermanentGraveBooking/Show', [
            'booking' => $permanentGraveBooking,
        ]);
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
     * Add a new valid member to a permanent grave
     */
    public function addValidMember(Request $request)
    {
        try {
            $request->validate([
                'permanent_grave_id' => 'required|exists:permanent_graves,id',
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'relationship' => 'required|string|max:50',
                'member_type' => 'required|in:Member,External',
                'contact_no' => 'nullable|string|max:20',
                'notes' => 'nullable|string|max:500'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            DB::beginTransaction();

            // Get the permanent grave
            $grave = PermanentGrave::findOrFail($request->permanent_grave_id);

            // Create the new valid member
            $validMember = ValidMember::create([
                'permanent_grave_id' => $request->permanent_grave_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'relationship' => $request->relationship,
                'member_type' => strtolower($request->member_type), // Convert to lowercase for database
                'grave_type' => 'permanent_grave',
                'contact_no' => $request->contact_no,
                'notes' => $request->notes,
                'is_active' => true,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            // Return the new member data
            $newMemberData = [
                'id' => $validMember->id,
                'full_name' => $validMember->full_name,
                'first_name' => $validMember->first_name,
                'last_name' => $validMember->last_name,
                'relationship' => $validMember->relationship,
                'member_type' => $validMember->member_type,
                'is_deceased' => $validMember->is_deceased,
                'death_date' => $validMember->death_date?->format('Y-m-d'),
                'burial_date' => $validMember->burial_date?->format('Y-m-d')
            ];

            // Return JSON response for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'New person added successfully to the grave.',
                    'newMember' => $newMemberData
                ]);
            }

            return back()->with([
                'success' => 'New person added successfully to the grave.',
                'newMember' => $newMemberData
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add valid member: ' . $e->getMessage());

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add new person. Please try again.',
                    'error' => $e->getMessage()
                ], 422);
            }

            return back()->withErrors([
                'error' => 'Failed to add new person. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Delete a pending booking
     */
    public function destroy(PermanentGraveBooking $permanentGraveBooking)
    {
        // Only allow deletion of pending bookings
        if ($permanentGraveBooking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be deleted.');
        }

        // Check if booking has any payments
        if ($permanentGraveBooking->payments()->exists()) {
            return back()->with('error', 'Cannot delete booking with payment records.');
        }

        try {
            $permanentGraveBooking->delete();
            return redirect()->route('graveyard.permanent-grave-bookings.index')
                ->with('success', 'Booking deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete permanent grave booking: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete booking. Please try again.');
        }
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
