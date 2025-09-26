<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\AnnualMaintenanceFee;
use Modules\Graveyard\Http\Requests\AnnualMaintenanceFeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AnnualMaintenanceFeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:access-graveyard']);
        $this->middleware(['permission:list-annual-maintenance-fees'])->only(['index']);
        $this->middleware(['permission:read-annual-maintenance-fees'])->only(['show']);
        $this->middleware(['permission:create-annual-maintenance-fees'])->only(['create', 'store']);
        $this->middleware(['permission:update-annual-maintenance-fees'])->only(['edit', 'update']);
        $this->middleware(['permission:delete-annual-maintenance-fees'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource
     */
    public function index(Request $request): Response
    {
        $perPage = $request->get('perPage', 25);
        $search = $request->get('search');
        $year = $request->get('year');
        $sort = $request->get('sort', 'year');
        $direction = $request->get('direction', 'desc');

        $query = AnnualMaintenanceFee::with(['creator', 'updater'])
            ->orderBy($sort, $direction);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('year', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($year) {
            $query->where('year', $year);
        }

        $fees = $query->paginate($perPage)->withQueryString();

        // Check for missing current year fee and prepare warning
        $currentYear = now()->year;
        $hasCurrentYearFee = AnnualMaintenanceFee::hasCurrentYearFee();
        $latestFeeYear = AnnualMaintenanceFee::getLatestFeeYear();

        $warning = null;
        if (!$hasCurrentYearFee && $latestFeeYear) {
            $permanentGraveFee = AnnualMaintenanceFee::getCurrentFee('permanent_grave');
            $nicheFee = AnnualMaintenanceFee::getCurrentFee('niche');

            $warning = [
                'message' => "No maintenance fee set for {$currentYear}. Using {$latestFeeYear} rates.",
                'current_year' => $currentYear,
                'fallback_year' => $latestFeeYear,
                'rates' => [
                    'permanent_grave' => $permanentGraveFee ? '₹ ' . number_format($permanentGraveFee, 2) : 'N/A',
                    'niche' => $nicheFee ? '₹ ' . number_format($nicheFee, 2) : 'N/A',
                ]
            ];
        }

        return Inertia::render('PagesGraveyard/AnnualMaintenanceFees/Index', [
            'data' => $fees,
            'filters' => $request->only(['search', 'year', 'perPage', 'sort', 'direction']),
            'years' => AnnualMaintenanceFee::distinct('year')->orderBy('year', 'desc')->pluck('year'),
            'warning' => $warning,
        ]);
    }

    /**
     * Show the form for creating a new resource
     */
    public function create(): Response
    {
        // Get the next year that doesn't have a fee set
        $currentYear = now()->year;
        $existingYears = AnnualMaintenanceFee::pluck('year')->toArray();

        $suggestedYear = $currentYear;
        while (in_array($suggestedYear, $existingYears)) {
            $suggestedYear++;
        }

        // Get the previous year's fees for reference
        $previousYear = AnnualMaintenanceFee::where('year', '<', $suggestedYear)
            ->orderBy('year', 'desc')
            ->first();

        return Inertia::render('PagesGraveyard/AnnualMaintenanceFees/Create', [
            'suggestedYear' => $suggestedYear,
            'previousFee' => $previousYear,
        ]);
    }

    /**
     * Store a newly created resource in storage
     */
    public function store(AnnualMaintenanceFeeRequest $request)
    {
        $fee = AnnualMaintenanceFee::create([
            ...$request->validated(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('graveyard.annual-maintenance-fees.index')
            ->with('success', "Annual maintenance fee for {$fee->year} created successfully.");
    }

    /**
     * Display the specified resource
     */
    public function show(AnnualMaintenanceFee $annualMaintenanceFee): Response
    {
        $annualMaintenanceFee->load(['creator', 'updater']);

        return Inertia::render('PagesGraveyard/AnnualMaintenanceFees/Show', [
            'fee' => $annualMaintenanceFee,
        ]);
    }

    /**
     * Show the form for editing the specified resource
     */
    public function edit(AnnualMaintenanceFee $annualMaintenanceFee): Response
    {
        if (!$annualMaintenanceFee->canBeEdited()) {
            return redirect()
                ->route('graveyard.annual-maintenance-fees.index')
                ->with('error', 'This maintenance fee cannot be edited as it has already been applied to members.');
        }

        return Inertia::render('PagesGraveyard/AnnualMaintenanceFees/Edit', [
            'fee' => $annualMaintenanceFee,
        ]);
    }

    /**
     * Update the specified resource in storage
     */
    public function update(AnnualMaintenanceFeeRequest $request, AnnualMaintenanceFee $annualMaintenanceFee)
    {
        if (!$annualMaintenanceFee->canBeEdited()) {
            return redirect()
                ->route('graveyard.annual-maintenance-fees.index')
                ->with('error', 'This maintenance fee cannot be edited as it has already been applied to members.');
        }

        $annualMaintenanceFee->update([
            ...$request->validated(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('graveyard.annual-maintenance-fees.index')
            ->with('success', "Annual maintenance fee for {$annualMaintenanceFee->year} updated successfully.");
    }

    /**
     * Remove the specified resource from storage
     */
    public function destroy(AnnualMaintenanceFee $annualMaintenanceFee)
    {
        if (!$annualMaintenanceFee->canBeDeleted()) {
            return redirect()
                ->route('graveyard.annual-maintenance-fees.index')
                ->with('error', 'This maintenance fee cannot be deleted as it has already been applied to members.');
        }

        $year = $annualMaintenanceFee->year;
        $annualMaintenanceFee->delete();

        return redirect()
            ->route('graveyard.annual-maintenance-fees.index')
            ->with('success', "Annual maintenance fee for {$year} deleted successfully.");
    }

}