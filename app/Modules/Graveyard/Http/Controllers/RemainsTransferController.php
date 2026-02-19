<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\RemainsTransfer;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use Modules\Graveyard\Models\Niche;
use Modules\Graveyard\Models\PermanentGrave;
use Modules\Members\Models\Relationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RemainsTransferController extends Controller
{
    /**
     * Display niche transfers
     */
    public function index(Request $request)
    {
        $this->authorize('list-remains-transfer');

        $query = RemainsTransfer::with([
            'fromTemporaryGrave',
            'fromBooking',
            'toNiche',
            'toPermanentGrave',
            'creator',
        ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->withStatus($request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('dead_first_name', 'like', "%{$search}%")
                    ->orWhere('dead_last_name', 'like', "%{$search}%")
                    ->orWhere('transfer_reference', 'like', "%{$search}%");
            });
        }

        if ($request->filled('due_soon')) {
            $query->dueSoon();
        }

        // Pagination
        $transfers = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('PagesGraveyard/RemainsTransfer/Index', [
            'transfers' => $transfers,
            'filters' => $request->only(['status', 'search', 'due_soon'])
        ]);
    }

    /**
     * Show niche transfer form
     */
    public function create(Request $request)
    {
        $this->authorize('read-remains-transfer');

        $bookingId = $request->get('booking_id');
        $selectedBooking = null;

        if ($bookingId) {
            $selectedBooking = TemporaryGraveBooking::with(['temporaryGrave'])
                ->findOrFail($bookingId);
        }

        // Get eligible bookings
        $eligibleBookings = TemporaryGraveBooking::eligibleForTransfer()
            ->with(['temporaryGrave'])
            ->get();
        // If a specific booking was requested, include it even if transfer_requested = true
        if ($selectedBooking && !$eligibleBookings->contains('id', $selectedBooking->id)) {
            // Add the selected booking to the list if it's not already there
            if ($selectedBooking->status === 'confirmed') {
                $eligibleBookings->prepend($selectedBooking);
            }
        }

        // Format the bookings for the frontend
        $eligibleBookings = $eligibleBookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'booking_reference' => $booking->booking_reference,
                'full_name' => $booking->dead_first_name . ' ' . $booking->dead_last_name,
                'grave_no' => $booking->temporaryGrave->grave_no,
                'buried_on' => $booking->buried_on,
                'expected_transfer_date' => $booking->expected_transfer_date,
                'is_overdue' => $booking->isTransferOverdue(),
                'temporary_grave' => [
                    'grave_no' => $booking->temporaryGrave->grave_no,
                    'section' => $booking->temporaryGrave->section,
                    'row_no' => $booking->temporaryGrave->row_no,
                ]
            ];
        });

        // Format selected booking
        if ($selectedBooking) {
            $selectedBooking = [
                'id' => $selectedBooking->id,
                'booking_reference' => $selectedBooking->booking_reference,
                'full_name' => $selectedBooking->full_name,
                'grave_no' => $selectedBooking->temporaryGrave->grave_no,
                'buried_on' => $selectedBooking->buried_on,
                'expected_transfer_date' => $selectedBooking->expected_transfer_date,
                'is_overdue' => $selectedBooking->isTransferOverdue(),
                'temporary_grave' => [
                    'grave_no' => $selectedBooking->temporaryGrave->grave_no,
                    'section' => $selectedBooking->temporaryGrave->section,
                    'row_no' => $selectedBooking->temporaryGrave->row_no,
                ]
            ];
        }

        // Get available niches with their valid members
        $availableNiches = Niche::where('status', 'available')
            ->where('is_active', true)
            ->with(['validMembers' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->get()
            ->map(function ($niche) {
                return [
                    'id' => $niche->id,
                    'niche_no' => $niche->niche_no,
                    'section' => $niche->section,
                    'row_no' => $niche->row_no,
                    'location' => $niche->location,
                    'owner_name' => $niche->owner_name,
                    'last_occupation_date' => $niche->last_occupation_date,
                    'cost' => $niche->cost,
                    'valid_members' => $niche->validMembers->map(function ($member) {
                        return [
                            'id' => $member->id,
                            'full_name' => $member->full_name,
                            'first_name' => $member->first_name,
                            'last_name' => $member->last_name,
                            'relationship' => $member->relationship,
                            'member_type' => $member->member_type,
                            'is_deceased' => $member->is_deceased,
                            'death_date' => $member->death_date,
                            'burial_date' => $member->burial_date,
                        ];
                    }),
                    'has_valid_members' => $niche->validMembers->count() > 0,
                ];
            });

        // Get available permanent graves for transfer destination
        $availablePermanentGraves = PermanentGrave::where('status', 'available')
            ->get()
            ->map(function ($grave) {
                return [
                    'id' => $grave->id,
                    'grave_no' => $grave->grave_no,
                    'section' => $grave->section,
                    'row_no' => $grave->row_no,
                    'owner_name' => $grave->owner_name ?? '',
                ];
            });

        return Inertia::render('PagesGraveyard/RemainsTransfer/Create', [
            'eligibleBookings' => $eligibleBookings,
            'availableNiches' => $availableNiches,
            'availablePermanentGraves' => $availablePermanentGraves,
            'relationships' => Relationship::all(),
            'selectedBooking' => $selectedBooking
        ]);
    }

    /**
     * Store niche transfer
     */
    public function store(Request $request)
    {
        $this->authorize('create-remains-transfer');

        $request->validate([
            'from_booking_id' => 'required|exists:temporary_grave_bookings,id',
            'transfer_type' => 'required|in:niche,permanent_grave,removal',
            'to_niche_id' => 'required_if:transfer_type,niche|nullable|exists:niches,id',
            'to_permanent_grave_id' => 'required_if:transfer_type,permanent_grave|nullable|exists:permanent_graves,id',
            'proposed_transfer_date' => 'required|date|after:today',
            'transfer_reason' => 'nullable|string|max:500',
            'applicant_name' => 'required_unless:transfer_type,removal|nullable|string|max:255',
            'contact_no' => 'required_unless:transfer_type,removal|nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'applicant_address' => 'nullable|string|max:500',
            'relationship_id' => 'required_unless:transfer_type,removal|nullable|exists:relationships,id',
        ]);

        try {
            DB::beginTransaction();

            // Get the temporary booking
            $booking = TemporaryGraveBooking::with(['temporaryGrave'])
                ->findOrFail($request->from_booking_id);

            if ($booking->status !== 'confirmed') {
                return back()->withErrors(['booking' => 'Only confirmed bookings can be transferred.']);
            }

            // Check if there's already an existing transfer request for this booking
            $existingTransfer = RemainsTransfer::where('from_booking_id', $request->from_booking_id)->first();
            if ($existingTransfer) {
                return back()->withErrors(['from_booking_id' => 'A transfer request already exists for this booking.']);
            }

            // Validate destination based on transfer type
            if ($request->transfer_type === 'niche') {
                $niche = Niche::findOrFail($request->to_niche_id);
                if (!$niche->isAvailable()) {
                    return back()->withErrors(['niche' => 'This niche is no longer available.']);
                }
            }

            // Create transfer record (costs will be calculated later when approved/reviewed)
            $transfer = RemainsTransfer::create([
                'from_temporary_grave_id' => $booking->temporary_grave_id,
                'from_booking_id' => $request->from_booking_id,
                'transfer_type' => $request->transfer_type,
                'to_niche_id' => $request->transfer_type === 'niche' ? $request->to_niche_id : null,
                'to_permanent_grave_id' => $request->transfer_type === 'permanent_grave' ? $request->to_permanent_grave_id : null,
                'proposed_transfer_date' => $request->proposed_transfer_date,
                'transfer_reason' => $request->transfer_reason,
                'applicant_name' => $request->applicant_name,
                'contact_no' => $request->contact_no,
                'contact_email' => $request->contact_email,
                'applicant_address' => $request->applicant_address,
                'relationship_id' => $request->relationship_id,
                'status' => 'pending',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Mark booking as having transfer requested
            $booking->update(['transfer_requested' => true]);

            DB::commit();

            return redirect()->route('graveyard.remains-transfers.show', $transfer->id)
                ->with('success', 'Remains transfer request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating remains transfer: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create transfer request.'])->withInput();
        }
    }

    /**
     * Show / Edit transfer details
     */
    public function show(RemainsTransfer $remainsTransfer)
    {
        $this->authorize('read-remains-transfer');

        $remainsTransfer->load([
            'fromTemporaryGrave',
            'fromBooking.temporaryGrave',
            'toNiche',
            'toPermanentGrave',
            'relationship',
            'creator',
            'updater',
            'payments'
        ]);

        // Include the currently selected niche even if its status is not 'available'
        $availableNiches = Niche::where(function ($q) use ($remainsTransfer) {
                $q->where('status', 'available')
                  ->orWhere('id', $remainsTransfer->to_niche_id);
            })
            ->where('is_active', true)
            ->with(['validMembers'])
            ->get()
            ->map(function ($niche) {
                return [
                    'id' => $niche->id,
                    'niche_no' => $niche->niche_no,
                    'section' => $niche->section,
                    'row_no' => $niche->row_no,
                    'location' => $niche->location,
                    'owner_name' => $niche->owner_name,
                    'last_occupation_date' => $niche->last_occupation_date,
                    'cost' => $niche->cost,
                ];
            });

        // Include the currently selected permanent grave even if unavailable
        $availablePermanentGraves = PermanentGrave::where(function ($q) use ($remainsTransfer) {
                $q->where('status', 'available')
                  ->orWhere('id', $remainsTransfer->to_permanent_grave_id);
            })
            ->get()
            ->map(function ($grave) {
                return [
                    'id' => $grave->id,
                    'grave_no' => $grave->grave_no,
                    'section' => $grave->section,
                    'row_no' => $grave->row_no,
                    'owner_name' => $grave->owner_name ?? '',
                ];
            });

        return Inertia::render('PagesGraveyard/RemainsTransfer/Edit', [
            'transfer' => $remainsTransfer,
            'relationships' => Relationship::all(),
            'availableNiches' => $availableNiches,
            'availablePermanentGraves' => $availablePermanentGraves,
        ]);
    }

    /**
     * Update transfer details
     */
    public function update(Request $request, RemainsTransfer $remainsTransfer)
    {
        $this->authorize('update-remains-transfer');

        $rules = [
            'proposed_transfer_date' => 'required|date',
            'transfer_reason' => 'nullable|string|max:500',
            'to_niche_id' => 'nullable|exists:niches,id',
            'to_permanent_grave_id' => 'nullable|exists:permanent_graves,id',
            'applicant_name' => 'nullable|string|max:255',
            'contact_no' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email',
            'applicant_address' => 'nullable|string|max:500',
            'relationship_id' => 'nullable|exists:relationships,id',
        ];

        if ($remainsTransfer->transfer_type === 'niche') {
            $rules['to_niche_id'] = 'required|exists:niches,id';
        }

        if ($remainsTransfer->transfer_type === 'permanent_grave') {
            $rules['to_permanent_grave_id'] = 'required|exists:permanent_graves,id';
        }

        if ($remainsTransfer->transfer_type !== 'removal') {
            $rules['applicant_name'] = 'required|string|max:255';
            $rules['contact_no'] = 'required|string|max:20';
            $rules['relationship_id'] = 'required|exists:relationships,id';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $remainsTransfer->update([
                'proposed_transfer_date' => $request->proposed_transfer_date,
                'transfer_reason' => $request->transfer_reason,
                'to_niche_id' => $remainsTransfer->transfer_type === 'niche' ? $request->to_niche_id : $remainsTransfer->to_niche_id,
                'to_permanent_grave_id' => $remainsTransfer->transfer_type === 'permanent_grave' ? $request->to_permanent_grave_id : $remainsTransfer->to_permanent_grave_id,
                'applicant_name' => $request->applicant_name,
                'contact_no' => $request->contact_no,
                'contact_email' => $request->contact_email,
                'applicant_address' => $request->applicant_address,
                'relationship_id' => $request->relationship_id,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('graveyard.remains-transfers.show', $remainsTransfer->id)
                ->with('success', 'Transfer updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating remains transfer: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update transfer request.'])->withInput();
        }
    }

    /**
     * Approve transfer
     */
    public function approve(Request $request, RemainsTransfer $remainsTransfer)
    {
        $this->authorize('update-remains-transfer');

        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($remainsTransfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be approved.');
        }

        $remainsTransfer->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'updated_by' => Auth::id()
        ]);

        return back()->with('success', 'Transfer approved successfully.');
    }

    /**
     * Reject transfer
     */
    public function reject(Request $request, RemainsTransfer $remainsTransfer)
    {
        $this->authorize('update-remains-transfer');

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($remainsTransfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be rejected.');
        }

        try {
            DB::beginTransaction();

            // Reject the transfer
            $remainsTransfer->update([
                'status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'updated_by' => Auth::id()
            ]);

            // Reset transfer requested flag on original booking
            $remainsTransfer->fromBooking->update(['transfer_requested' => false]);

            DB::commit();

            return back()->with('success', 'Transfer rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to reject transfer.');
        }
    }

    /**
     * Complete transfer
     */
    public function complete(RemainsTransfer $remainsTransfer)
    {
        $this->authorize('update-remains-transfer');

        if (!$remainsTransfer->canBeCompleted()) {
            return back()->with('error', 'This transfer cannot be completed yet. Check status, payment, and date requirements.');
        }

        if ($remainsTransfer->complete()) {
            return back()->with('success', 'Transfer completed successfully. The deceased has been moved to the niche.');
        }

        return back()->with('error', 'Failed to complete transfer.');
    }

    /**
     * Cancel transfer
     */
    public function cancel(Request $request, RemainsTransfer $remainsTransfer)
    {
        $this->authorize('update-remains-transfer');

        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $remainsTransfer->update([
                'status' => 'cancelled',
                'rejection_reason' => $request->cancellation_reason,
                'updated_by' => Auth::id()
            ]);

            // Reset transfer requested flag on original booking
            $remainsTransfer->fromBooking->update(['transfer_requested' => false]);

            DB::commit();

            return back()->with('success', 'Transfer cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel transfer.');
        }
    }

    /**
     * Get transfer statistics (API endpoint)
     */
    public function statistics()
    {
        $this->authorize('read-remains-transfer');

        $stats = [
            'pending' => RemainsTransfer::pending()->count(),
            'approved' => RemainsTransfer::approved()->count(),
            'completed' => RemainsTransfer::where('status', 'completed')->count(),
            'due_soon' => RemainsTransfer::dueSoon()->count(),
            'total_transfers' => RemainsTransfer::count()
        ];

        return response()->json($stats);
    }
}
