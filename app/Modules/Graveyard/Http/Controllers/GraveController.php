<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GraveController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('PagesGraveyard/Graves/Index', []);
    }

    public function store(Request $request) { return back()->with('success', 'Grave created successfully.'); }
    public function show($grave) { return Inertia::render('PagesGraveyard/Graves/Show', []); }
    public function update(Request $request, $grave) { return back()->with('success', 'Grave updated successfully.'); }
    public function destroy($grave) { return back()->with('success', 'Grave deleted successfully.'); }
    public function restore($id) { return back()->with('success', 'Grave restored successfully.'); }
    public function forceDelete($id) { return back()->with('success', 'Grave permanently deleted.'); }
}
