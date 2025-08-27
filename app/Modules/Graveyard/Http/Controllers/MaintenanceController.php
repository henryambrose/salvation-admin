<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MaintenanceController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('PagesGraveyard/Maintenance/Index', []);
    }

    public function store(Request $request) { return back()->with('success', 'Maintenance record created successfully.'); }
    public function show($maintenance) { return Inertia::render('PagesGraveyard/Maintenance/Show', []); }
    public function update(Request $request, $maintenance) { return back()->with('success', 'Maintenance record updated successfully.'); }
    public function destroy($maintenance) { return back()->with('success', 'Maintenance record deleted successfully.'); }
    public function restore($id) { return back()->with('success', 'Maintenance record restored successfully.'); }
    public function forceDelete($id) { return back()->with('success', 'Maintenance record permanently deleted.'); }
}
