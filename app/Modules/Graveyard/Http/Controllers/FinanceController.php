<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinanceController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('PagesGraveyard/Finances/Index', []);
    }

    public function store(Request $request) { return back()->with('success', 'Financial transaction created successfully.'); }
    public function show($transaction) { return Inertia::render('PagesGraveyard/Finances/Show', []); }
    public function update(Request $request, $transaction) { return back()->with('success', 'Financial transaction updated successfully.'); }
    public function destroy($transaction) { return back()->with('success', 'Financial transaction deleted successfully.'); }
    public function restore($id) { return back()->with('success', 'Financial transaction restored successfully.'); }
    public function forceDelete($id) { return back()->with('success', 'Financial transaction permanently deleted.'); }
}
