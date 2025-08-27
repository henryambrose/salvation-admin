<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VisitorController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('PagesGraveyard/Visitors/Index', []);
    }

    public function store(Request $request) { return back()->with('success', 'Visitor record created successfully.'); }
    public function show($visitor) { return Inertia::render('PagesGraveyard/Visitors/Show', []); }
    public function update(Request $request, $visitor) { return back()->with('success', 'Visitor record updated successfully.'); }
    public function destroy($visitor) { return back()->with('success', 'Visitor record deleted successfully.'); }
    public function restore($id) { return back()->with('success', 'Visitor record restored successfully.'); }
    public function forceDelete($id) { return back()->with('success', 'Visitor record permanently deleted.'); }
}
