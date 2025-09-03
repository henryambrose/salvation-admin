<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\NicheTransfer;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use Modules\Graveyard\Models\Niche;
use Modules\Graveyard\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class NicheTransferController extends Controller
{
    /**
     * Display niche transfers
     */
    public function index(Request $request)
    {
        $query = NicheTransfer::with([
            'fromTemporaryGrave',
            'fromBooking',
            'toNiche',
            'creator',
            'approver'
        ]);

        // Apply filters
        if ($request->filled('status')) {
            $query->withStatus($request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('deceased_first_name', 'like', "%{$search}%")
                  ->orWhere('deceased_last_name', 'like', "%{$search}%")
                  ->orWhere('transfer_reference', 'like', "%{$search}%");
            });
        }

        if ($request->filled('due_soon')) {
            $query->dueSoon();
        }

        // Pagination
        $transfers = $query->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('PagesGraveyard/NicheTransfer/Index', [
            'transfers' => $transfers,
            'filters' => $request->only(['status', 'search', 'due_soon'])
        ]);
    }

    /**
     * Show niche transfer form
     */
    public function create(Request $request)
    {
        $bookingId = $request->get('booking_id');
        $selectedBooking = null;

        if ($bookingId) {
            $selectedBooking = TemporaryGraveBooking::with(['temporaryGrave'])
                ->findOrFail($bookingId);
        }

        return Inertia::render('PagesGraveyard/NicheTransfer/Create', [
            'serviceTypes' => ServiceType::active()->byCategory('grave')->get(),
            'eligibleBookings' => TemporaryGraveBooking::eligibleForTransfer()
                ->with(['temporaryGrave'])
                ->get(),
            'availableNiches' => Niche::where('is_available', true)->get(),
            'selectedBooking' => $selectedBooking
        ]);
    }

    /**
     * Store niche transfer
     */
    public function store(Request $request)
    {
        $request->validate([
            'from_booking_id' => 'required|exists:temporary_grave_bookings,id',
            'to_niche_id' => 'required|exists:niches,id',
            'proposed_transfer_date' => 'required|date|after:today',
            'transfer_reason' => 'required|string|max:500',
            'transfer_applicant_name' => 'required|string|max:255',
            'transfer_contact_no' => 'required|string|max:20',
            'transfer_contact_email' => 'nullable|email',
            'relationship_to_deceased' => 'nullable|string|max:100',
            'applicant_address' => 'nullable|string|max:500',
            'selected_services' => 'nullable|array',
            'selected_services.*' => 'exists:service_types,id'
        ]);

        try {
            DB::beginTransaction();

            // Get the temporary booking
            $booking = TemporaryGraveBooking::with(['temporaryGrave'])
                ->findOrFail($request->from_booking_id);

            if ($booking->status !== 'confirmed') {
                return back()->withErrors(['booking' => 'Only confirmed bookings can be transferred.']);
            }

            if ($booking->transfer_requested) {
                return back()->withErrors(['booking' => 'A transfer request already exists for this booking.']);
            }

            // Check if niche is still available
            $niche = Niche::findOrFail($request->to_niche_id);
            if (!$niche->is_available) {
                return back()->withErrors(['niche' => 'This niche is no longer available.']);
            }

            // Create transfer record
            $transfer = NicheTransfer::create([
                'from_temporary_grave_id' => $booking->temporary_grave_id,
                'from_booking_id' => $request->from_booking_id,
                'to_niche_id' => $request->to_niche_id,
                'proposed_transfer_date' => $request->proposed_transfer_date,
                'transfer_reason' => $request->transfer_reason,
                'transfer_applicant_name' => $request->transfer_applicant_name,
                'transfer_contact_no' => $request->transfer_contact_no,
                'transfer_contact_email' => $request->transfer_contact_email,
                'relationship_to_deceased' => $request->relationship_to_deceased,
                'applicant_address' => $request->applicant_address,
                'selected_services' => $request->selected_services,
                'niche_cost' => $niche->cost,
                'status' => 'pending',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Calculate transfer cost from services
            if ($request->selected_services) {
                $serviceCost = ServiceType::whereIn('id', $request->selected_services)->sum('cost');
                $transfer->update([
                    'transfer_cost' => $serviceCost,
                    'total_cost' => $transfer->niche_cost + $serviceCost,
                    'balance_amount' => $transfer->niche_cost + $serviceCost
                ]);
            }

            // Mark booking as having transfer requested
            $booking->update(['transfer_requested' => true]);

            DB::commit();

            return redirect()->route('graveyard.niche-transfers.show', $transfer->id)
                ->with('success', 'Niche transfer request created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create transfer request.'])->withInput();
        }
    }

    /**
     * Show transfer details
     */
    public function show(NicheTransfer $nicheTransfer)
    {
        $nicheTransfer->load([
            'fromTemporaryGrave',
            'fromBooking.temporaryGrave',
            'toNiche',
            'creator',
            'updater',
            'approver',
            'rejecter'
        ]);

        return Inertia::render('PagesGraveyard/NicheTransfer/Show', [
            'transfer' => $nicheTransfer
        ]);
    }

    /**
     * Approve transfer
     */
    public function approve(Request $request, NicheTransfer $nicheTransfer)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        if ($nicheTransfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be approved.');
        }

        if ($nicheTransfer->approve($request->admin_notes)) {
            return back()->with('success', 'Transfer approved successfully.');
        }

        return back()->with('error', 'Failed to approve transfer.');
    }

    /**
     * Reject transfer
     */
    public function reject(Request $request, NicheTransfer $nicheTransfer)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        if ($nicheTransfer->status !== 'pending') {
            return back()->with('error', 'Only pending transfers can be rejected.');
        }

        try {
            DB::beginTransaction();

            // Reject the transfer
            if ($nicheTransfer->reject($request->rejection_reason)) {
                // Reset transfer requested flag on original booking
                $nicheTransfer->fromBooking->update(['transfer_requested' => false]);
            }

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
    public function complete(NicheTransfer $nicheTransfer)
    {
        if (!$nicheTransfer->canBeCompleted()) {
            return back()->with('error', 'This transfer cannot be completed yet. Check status, payment, and date requirements.');
        }

        if ($nicheTransfer->complete()) {
            return back()->with('success', 'Transfer completed successfully. The deceased has been moved to the niche.');
        }

        return back()->with('error', 'Failed to complete transfer.');
    }

    /**
     * Cancel transfer
     */
    public function cancel(Request $request, NicheTransfer $nicheTransfer)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $nicheTransfer->update([
                'status' => 'cancelled',
                'rejection_reason' => $request->cancellation_reason,
                'updated_by' => auth()->id()
            ]);

            // Reset transfer requested flag on original booking
            $nicheTransfer->fromBooking->update(['transfer_requested' => false]);

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
        $stats = [
            'pending' => NicheTransfer::pending()->count(),
            'approved' => NicheTransfer::approved()->count(),
            'completed' => NicheTransfer::where('status', 'completed')->count(),
            'due_soon' => NicheTransfer::dueSoon()->count(),
            'total_transfers' => NicheTransfer::count()
        ];

        return response()->json($stats);
    }
}