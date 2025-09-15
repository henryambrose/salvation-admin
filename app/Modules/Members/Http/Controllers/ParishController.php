<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Members\Http\Requests\StoreParishRequest;
use Modules\Members\Http\Requests\UpdateParishRequest;
use Modules\Members\Models\Parish;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ParishController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        DB::enableQueryLog();
        $query = Parish::query();

        // Handle archived records
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($search = $request->input('search')) {
            $query->whereRaw(
                "CONCAT(
          COALESCE(deanery, ''),
          COALESCE(name, ''),
          COALESCE(code, ''),
          COALESCE(town, ''),
          COALESCE(address, '')
          ) LIKE ?",
                ["%$search%"]
            );
        }

        if ($sort = $request->input('sort')) {
            $query->orderBy($sort, $request->input('direction', 'asc'));
        } else {
            $query->orderBy('id', 'asc');
        }

        $perPage = $request->input('perPage', 10);

        return Inertia::render('parish/Index', [
            'fetchUrl' => route('parish.index'),
            'parishes' => $query->paginate($perPage)->appends($request->query()),
            'filters' => $request->only(['search', 'sort', 'direction', 'perPage', 'isArchived']),
            'query' => DB::getQueryLog(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('parish/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreParishRequest $request)
    {
        Parish::create($request->validated());

        return redirect()->route('parish.index')->with('success', 'Parish created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Parish $parish)
    {
        // Optionally implement if needed
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Parish $parish)
    {
        return Inertia::render('parish/Edit', [
            'parish' => $parish,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateParishRequest $request, Parish $parish)
    {
        $parish->update($request->validated());

        return redirect()->route('parish.index')->with('success', 'Parish updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Parish $parish)
    {
        $parish->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('parish.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'Parish deleted successfully.');
    }

    public function restore($id)
    {
        $parish = Parish::onlyTrashed()->findOrFail($id);
        $parish->restore();

        return redirect()->route('parish.index')->with('success', 'Parish restored successfully.');
    }

    /**
     * Export parishes to CSV.
     */
    public function export(Request $request)
    {
        try {
            $this->authorize('viewAny', Parish::class);

            $query = Parish::with(['zone']);

            if ($request->boolean('isArchived')) {
                $query->onlyTrashed();
            } else {
                $query->withoutTrashed();
            }

            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhereHas('zone', function ($zoneQuery) use ($search) {
                            $zoneQuery->where('name', 'like', "%$search%");
                        });
                });
            }

            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['id', 'deanery', 'name', 'town', 'zone.name'];
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');

            if (in_array($sort, $allowedSortColumns)) {
                if ($sort === 'zone.name') {
                    $query->join('zones', 'parishes.zone_id', '=', 'zones.id')
                        ->orderBy('zones.name', $direction);
                } else {
                    $query->orderBy($sort, $direction);
                }
            } else {
                $query->orderBy('id', 'asc');
            }

            // Streamed CSV keeps memory flat
            return response()->streamDownload(function () use ($query) {
                // Clear any output buffers to prevent extra whitespace
                while (ob_get_level()) {
                    ob_end_clean();
                }

                $out = fopen('php://output', 'w');

                fputcsv($out, [
                    'ID',
                    'Deanery',
                    'Parish Name',
                    'Code',
                    'Town',
                    'Address'
                ]);

                foreach ($query->cursor() as $item) {
                    fputcsv($out, [
                        $item->id,
                        $item->deanery ?? '',
                        $item->name ?? '',
                        $item->code ?? '',
                        $item->town ?? '',
                        $item->address ?? '',
                    ]);
                }

                fclose($out);
            }, 'parishes_' . now()->format('Y-m-d_H-i-s') . '.csv', [
                'Content-Type' => 'text/csv',
                'Cache-Control' => 'no-store, no-cache',
            ]);
        } catch (\Exception $e) {
            Log::error('Parish Export failed: ' . $e->getMessage());
            return response()->json(['error' => 'Export failed: ' . $e->getMessage()], 500);
        }
    }
}
