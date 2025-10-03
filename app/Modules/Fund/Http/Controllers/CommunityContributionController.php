<?php

namespace Modules\Fund\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Fund\Models\CommunityContribution;
use Modules\Fund\Models\CommunityContributionType;
use Modules\Members\Models\User;
use Modules\Fund\Http\Requests\StoreCommunityContributionRequest;
use Modules\Fund\Http\Requests\UpdateCommunityContributionRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CommunityContributionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', CommunityContribution::class);
        $query = CommunityContribution::with(['contributionType', 'collectedBy', 'creator'])
            ->orderBy('collection_date', 'desc');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhereHas('contributionType', function ($typeQuery) use ($search) {
                        $typeQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('collectedBy', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Handle archived filter
        if ($request->get('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        // Apply filters
        if ($request->filled('contribution_type_id')) {
            $query->where('contribution_type_id', $request->contribution_type_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('collection_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('collection_date', '<=', $request->end_date);
        }

        // Filter by year if provided
        if ($request->filled('year')) {
            $query->whereYear('collection_date', $request->year);
        } else {
            // Default to current year
            $query->whereYear('collection_date', now()->year);
        }

        $perPage = $request->input('perPage', 10);
        $contributions = $query->paginate($perPage)->appends($request->query());

        // Get summary statistics
        $stats = [
            'total_amount' => CommunityContribution::active()->currentYear()->sum('amount'),
            'total_count' => CommunityContribution::active()->currentYear()->count(),
            'verified_amount' => CommunityContribution::withStatus('verified')->currentYear()->sum('amount'),
            'pending_count' => CommunityContribution::withStatus('recorded')->currentYear()->count(),
        ];

        return Inertia::render('CommunityContribution/Index', [
            'contributions' => $contributions,
            'filters' => $request->only(['search', 'contribution_type_id', 'status', 'start_date', 'end_date', 'year', 'perPage', 'isArchived']),
            'contributionTypes' => CommunityContributionType::active()->ordered()->get(),
            'statusOptions' => CommunityContribution::getStatusOptions(),
            'stats' => $stats
        ]);
    }

    public function create()
    {
        return Inertia::render('CommunityContribution/Create', [
            'contributionTypes' => CommunityContributionType::active()->ordered()->get(),
            'users' => User::select('id', 'name')->orderBy('name')->get(),
            'statusOptions' => CommunityContribution::getStatusOptions(),
        ]);
    }

    public function store(StoreCommunityContributionRequest $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            $validatedData['created_by'] = Auth::id();

            CommunityContribution::create($validatedData);

            DB::commit();

            return redirect()->route('fund.community-contributions.index')
                ->with('success', 'Community contribution recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating community contribution: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Failed to record contribution. Please try again.'])
                ->withInput();
        }
    }

    public function show(CommunityContribution $communityContribution)
    {
        $communityContribution->load(['contributionType', 'collectedBy', 'creator', 'updater']);

        return Inertia::render('CommunityContribution/Show', [
            'contribution' => $communityContribution
        ]);
    }

    public function edit(CommunityContribution $communityContribution)
    {
        $communityContribution->load(['contributionType', 'collectedBy']);

        return Inertia::render('CommunityContribution/Edit', [
            'contribution' => $communityContribution,
            'contributionTypes' => CommunityContributionType::active()->ordered()->get(),
            'users' => User::select('id', 'name')->orderBy('name')->get(),
            'statusOptions' => CommunityContribution::getStatusOptions(),
        ]);
    }

    public function update(UpdateCommunityContributionRequest $request, CommunityContribution $communityContribution)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            $validatedData['updated_by'] = Auth::id();

            $communityContribution->update($validatedData);

            DB::commit();

            return redirect()->route('fund.community-contributions.index')
                ->with('success', 'Community contribution updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating community contribution: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update contribution. Please try again.'])
                ->withInput();
        }
    }

    public function destroy(Request $request, CommunityContribution $communityContribution)
    {
        try {
            $communityContribution->delete();

            $page = $request->input('page', 1);
            $perPage = $request->input('perPage', 10);

            return redirect()->route('fund.community-contributions.index', array_merge(
                $request->only(['search', 'contribution_type_id', 'status', 'start_date', 'end_date', 'year']),
                ['page' => $page, 'perPage' => $perPage]
            ))->with('success', 'Community contribution deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting community contribution: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete contribution. Please try again.']);
        }
    }

    public function restore($id)
    {
        try {
            $contribution = CommunityContribution::onlyTrashed()->findOrFail($id);
            $contribution->restore();

            return redirect()->route('fund.community-contributions.index')
                ->with('success', 'Community contribution restored successfully.');
        } catch (\Exception $e) {
            Log::error('Error restoring community contribution: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Failed to restore contribution. Please try again.']);
        }
    }

    /**
     * Update contribution status
     */
    public function updateStatus(Request $request, CommunityContribution $communityContribution)
    {
        $request->validate([
            'status' => 'required|in:recorded,verified,archived'
        ]);

        try {
            $communityContribution->update([
                'status' => $request->status,
                'updated_by' => Auth::id()
            ]);

            return redirect()->back()
                ->with('success', 'Contribution status updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating contribution status: ' . $e->getMessage());

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update status. Please try again.']);
        }
    }

    /**
     * Get contribution statistics for reports
     */
    public function getStats(Request $request)
    {
        $year = $request->input('year', now()->year);
        $contributionTypeId = $request->input('contribution_type_id');

        $query = CommunityContribution::active()->forYear($year);

        if ($contributionTypeId) {
            $query->where('contribution_type_id', $contributionTypeId);
        }

        $stats = [
            'total_amount' => $query->sum('amount'),
            'total_count' => $query->count(),
            'verified_amount' => $query->clone()->where('status', 'verified')->sum('amount'),
            'pending_count' => $query->clone()->where('status', 'recorded')->count(),
            'monthly_breakdown' => $query->clone()
                ->selectRaw('MONTH(collection_date) as month, SUM(amount) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
        ];

        return response()->json($stats);
    }
}
