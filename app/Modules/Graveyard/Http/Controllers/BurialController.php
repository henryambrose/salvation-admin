<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BurialController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('PagesGraveyard/Burials/Index', []);
    }

    public function store(Request $request) { return back()->with('success', 'Burial created successfully.'); }
    public function show($burial) { return Inertia::render('PagesGraveyard/Burials/Show', []); }
    public function update(Request $request, $burial) { return back()->with('success', 'Burial updated successfully.'); }
    public function destroy($burial) { return back()->with('success', 'Burial deleted successfully.'); }
    public function restore($id) { return back()->with('success', 'Burial restored successfully.'); }
    public function forceDelete($id) { return back()->with('success', 'Burial permanently deleted.'); }
}
