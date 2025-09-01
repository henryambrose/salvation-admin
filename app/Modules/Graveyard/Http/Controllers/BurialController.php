<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Modules\Graveyard\Models\GraveBooking;
use Modules\Graveyard\Models\PermanentGrave;
use Modules\Graveyard\Models\TemporaryGrave;
use Modules\Graveyard\Models\ServiceType;
use Modules\Graveyard\Models\BookingService;
use Modules\Members\Models\Member;
use Modules\Members\Models\Gender;
use Modules\Members\Models\Parish;
use Modules\Fund\Models\PaymentMethod;
use Carbon\Carbon;

class BurialController extends Controller
{
    /**
     * Display a listing of grave bookings
     */
    public function index(Request $request)
    {
        $query = GraveBooking::with([
            'permanentGrave', 
            'temporaryGrave', 
            'member', 
            'applicantMember', 
            'gender', 
            'parish',
            'bookingServices.serviceType'
        ]);

        // Archive logic
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('dead_first_name', 'like', "%{$search}%")
                  ->orWhere('dead_last_name', 'like', "%{$search}%")
                  ->orWhere('permit_no', 'like', "%{$search}%")
                  ->orWhere('applicant_name', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($memberQuery) use ($search) {
                      $memberQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('middle_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('applicantMember', function ($memberQuery) use ($search) {
                      $memberQuery->where('first_name', 'like', "%{$search}%")
                                  ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply filters
        if ($request->filled('grave_type')) {
            $query->where('grave_type', $request->grave_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('buried_on', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('buried_on', '<=', $request->end_date);
        }

        if ($request->filled('parish_id')) {
            $query->where('parish_id', $request->parish_id);
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'buried_on');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Pagination
        $perPage = $request->get('perPage', 10);
        $bookings = $query->paginate($perPage);

        // Get filter options
        $genders = Gender::orderBy('name')->get();
        $parishes = Parish::orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('name')->get();

        $filters = [
            'search' => $request->search,
            'grave_type' => $request->grave_type,
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'parish_id' => $request->parish_id,
            'sort' => $sortBy,
            'direction' => $sortDirection,
            'perPage' => $perPage,
            'isArchived' => $request->input('isArchived', 'false'),
        ];

        return Inertia::render('PagesGraveyard/Burials/Index', [
            'data' => $bookings,
            'filters' => $filters,
            'genders' => $genders,
            'parishes' => $parishes,
            'paymentMethods' => $paymentMethods,
            'fetchUrl' => route('graveyard.burials.index'),
        ]);
    }

    /**
     * Show the form for creating a new grave booking
     */
    public function create()
    {
        // Get available graves
        $availablePermanentGraves = PermanentGrave::available()
            ->orderBy('section', 'asc')
            ->orderBy('row_no', 'asc')
            ->orderBy('grave_no', 'asc')
            ->get();

        $availableTemporaryGraves = TemporaryGrave::available()
            ->orderBy('section', 'asc')
            ->orderBy('row_no', 'asc')
            ->orderBy('grave_no', 'asc')
            ->get();

        // Get service types
        $serviceTypes = ServiceType::active()
            ->ordered()
            ->get()
            ->groupBy('category');

        // Get form options
        $genders = Gender::orderBy('name')->get();
        $parishes = Parish::orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('name')->get();

        // Get members for search
        $members = Member::active()
            ->select('id', 'first_name', 'last_name', 'family_no')
            ->orderBy('first_name')
            ->get();

        return Inertia::render('PagesGraveyard/Burials/Create', [
            'availablePermanentGraves' => $availablePermanentGraves,
            'availableTemporaryGraves' => $availableTemporaryGraves,
            'serviceTypes' => $serviceTypes,
            'genders' => $genders,
            'parishes' => $parishes,
            'paymentMethods' => $paymentMethods,
            'members' => $members,
        ]);
    }

    /**
     * Store a newly created grave booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'grave_type' => 'required|in:permanent,temporary',
            'dead_first_name' => 'required|string|max:255',
            'dead_last_name' => 'required|string|max:255',
            'died_on' => 'required|date',
            'buried_on' => 'required|date|after_or_equal:died_on',
            'gender_id' => 'required|exists:genders,id',
            'relationship' => 'required|in:member,non_member',
            'applicant_type' => 'required|in:member,non_member',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        try {
            DB::beginTransaction();

            $bookingData = $request->all();
            $bookingData['created_by'] = Auth::id();
            $bookingData['updated_by'] = Auth::id();

            $booking = GraveBooking::create($bookingData);
            DB::commit();

            return redirect()->route('graveyard.burials.index')
                ->with('success', 'Grave booking created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create grave booking.'])->withInput();
        }
    }

    public function show($burial) { 
        return Inertia::render('PagesGraveyard/Burials/Show', []); 
    }
    
    public function update(Request $request, $burial) { 
        return back()->with('success', 'Burial updated successfully.'); 
    }
    
    public function destroy($burial) { 
        return back()->with('success', 'Burial deleted successfully.'); 
    }
    
    public function restore($id) { 
        return back()->with('success', 'Burial restored successfully.'); 
    }
    
    public function forceDelete($id) { 
        return back()->with('success', 'Burial permanently deleted.'); 
    }
}
