<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SectionController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('PagesGraveyard/Sections/Index', []);
    }

    public function store(Request $request) { return back()->with('success', 'Section created successfully.'); }
    public function show($section) { return Inertia::render('PagesGraveyard/Sections/Show', []); }
    public function update(Request $request, $section) { return back()->with('success', 'Section updated successfully.'); }
    public function destroy($section) { return back()->with('success', 'Section deleted successfully.'); }
    public function restore($id) { return back()->with('success', 'Section restored successfully.'); }
    public function forceDelete($id) { return back()->with('success', 'Section permanently deleted.'); }
}
