<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgeGroupRequest;
use App\Http\Requests\UpdateAgeGroupRequest;
use App\Models\AgeGroup;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Inertia\Response;


class AgeGroupController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request): Response
  {

    $query = AgeGroup::query();

    if ($search = $request->input('search')) {
      $query->where('name', 'like', "%$search%");
    }

    if ($sort = $request->input('sort')) {
      $query->orderBy($sort, $request->input('direction', 'asc'));
    } else {
      $query->orderBy('id', 'asc');
    }

    $perPage = $request->input('perPage', 10);

    return Inertia::render('age_group/Index', [
      'fetchUrl' => route('age-group.index'),
      'ageGroups' => $query->paginate($perPage)->appends($request->query()),
      'filters' => $request->only(['search', 'sort', 'direction', 'perPage']),
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    return Inertia::render('age_group/Create');
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreAgeGroupRequest $request)
  {
    AgeGroup::create($request->validated());

    return redirect()->route('age-group.index')->with('success', 'Age Group created successfully.');
  }

  /**
   * Display the specified resource.
   */
  public function show(AgeGroup $ageGroup)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(AgeGroup $ageGroup)
  {
    return Inertia::render('age_group/Edit', [
      'ageGroup' => $ageGroup,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(UpdateAgeGroupRequest $request, AgeGroup $ageGroup)
  {
    $ageGroup->update($request->validated());

    return redirect()->route('age-group.index')->with('success', 'Age Group updated successfully.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(AgeGroup $ageGroup)
  {
    $ageGroup->delete();

    return redirect()->route('age-group.index')->with('success', 'Age Group deleted successfully.');
  }
}
