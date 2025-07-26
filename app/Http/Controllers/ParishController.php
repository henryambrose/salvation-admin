<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParishRequest;
use App\Http\Requests\UpdateParishRequest;
use App\Models\Parish;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;

class ParishController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request): Response
  {
    \DB::enableQueryLog();
    $query = Parish::query();

    if ($search = $request->input('search')) {
      $query->whereRaw(
          "CONCAT(
          COALESCE(deanery, ''),
          COALESCE(name, ''),
          COALESCE(code, ''),
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
      'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
      'query' => \DB::getQueryLog(),
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
  public function destroy(Parish $parish)
  {
    $parish->delete();

    return redirect()->route('parish.index')->with('success', 'Parish deleted successfully.');
  }
}
