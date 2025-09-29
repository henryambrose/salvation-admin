<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\ObituaryBackgroundTheme;
use Modules\Graveyard\Services\ObituaryBackgroundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ObituaryBackgroundThemeController extends Controller
{
    protected ObituaryBackgroundService $backgroundService;

    public function __construct(ObituaryBackgroundService $backgroundService)
    {
        $this->backgroundService = $backgroundService;
    }

    /**
     * Display a listing of background themes
     */
    public function index(Request $request): Response
    {
        $query = ObituaryBackgroundTheme::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('key', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tier')) {
            $query->where('tier', $request->tier);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortDirection = $request->get('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        $themes = $query->paginate(15)->withQueryString();

        return Inertia::render('PagesGraveyard/ObituaryBackgroundThemes/Index', [
            'themes' => $themes,
            'filters' => $request->only(['search', 'tier', 'type', 'status', 'sort_by', 'sort_direction']),
            'stats' => [
                'total' => ObituaryBackgroundTheme::count(),
                'active' => ObituaryBackgroundTheme::where('is_active', true)->count(),
                // 'basic' => ObituaryBackgroundTheme::where('tier', 'basic')->count(),
                // 'premium' => ObituaryBackgroundTheme::where('tier', 'premium')->count(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new theme
     */
    public function create(): Response
    {
        return Inertia::render('PagesGraveyard/ObituaryBackgroundThemes/Create');
    }

    /**
     * Store a newly created theme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:obituary_background_themes,key',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:color,gradient,pattern,image',
            // 'tier' => 'required|in:basic,premium',
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|file|image|max:5120', // 5MB max
            'style_properties' => 'nullable|array',
            'background_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? ObituaryBackgroundTheme::max('sort_order') + 1;

        // Handle file upload
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . str_replace(' ', '_', $validated['key']) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('backgrounds', $filename, 'public');
            $validated['image_path'] = '/storage/' . $path;

            // Update style_properties for image type
            if ($validated['type'] === 'image') {
                $validated['style_properties'] = array_merge($validated['style_properties'] ?? [], [
                    'backgroundImage' => "url('{$validated['image_path']}')",
                    'backgroundSize' => 'cover',
                    'backgroundPosition' => 'center',
                    'backgroundRepeat' => 'no-repeat',
                    'backgroundColor' => $validated['background_color'] ?? '#ffffff',
                ]);
            }
        }

        // Remove image_file from validated data as it's not a database column
        unset($validated['image_file']);

        $theme = $this->backgroundService->createTheme($validated);

        return redirect()->route('graveyard.obituary-background-themes.index')
            ->with('success', 'Background theme created successfully.');
    }

    /**
     * Display the specified theme
     */
    public function show(ObituaryBackgroundTheme $theme): Response
    {
        return Inertia::render('PagesGraveyard/ObituaryBackgroundThemes/Show', [
            'theme' => $theme->toFrontendArray(),
        ]);
    }

    /**
     * Show the form for editing the theme
     */
    public function edit(ObituaryBackgroundTheme $theme): Response
    {
        return Inertia::render('PagesGraveyard/ObituaryBackgroundThemes/Edit', [
            'theme' => $theme,
        ]);
    }

    /**
     * Update the specified theme
     */
    public function update(Request $request, ObituaryBackgroundTheme $theme)
    {
        $validated = $request->validate([
            'key' => ['required', 'string', 'max:255', Rule::unique('obituary_background_themes', 'key')->ignore($theme->id)],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:color,gradient,pattern,image',
            // 'tier' => 'required|in:basic,premium',
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|file|image|max:5120', // 5MB max
            'style_properties' => 'nullable|array',
            'background_color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        // Handle file upload
        if ($request->hasFile('image_file')) {
            // Delete old file if exists
            if ($theme->image_path && file_exists(public_path($theme->image_path))) {
                unlink(public_path($theme->image_path));
            }

            $file = $request->file('image_file');
            $filename = time() . '_' . str_replace(' ', '_', $validated['key']) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('backgrounds', $filename, 'public');
            $validated['image_path'] = '/storage/' . $path;

            // Update style_properties for image type
            if ($validated['type'] === 'image') {
                $validated['style_properties'] = array_merge($validated['style_properties'] ?? [], [
                    'backgroundImage' => "url('{$validated['image_path']}')",
                    'backgroundSize' => 'cover',
                    'backgroundPosition' => 'center',
                    'backgroundRepeat' => 'no-repeat',
                    'backgroundColor' => $validated['background_color'] ?? '#ffffff',
                ]);
            }
        }

        // Remove image_file from validated data as it's not a database column
        unset($validated['image_file']);

        $theme->update($validated);

        return redirect()->route('graveyard.obituary-background-themes.index')
            ->with('success', 'Background theme updated successfully.');
    }

    /**
     * Remove the specified theme
     */
    public function destroy(ObituaryBackgroundTheme $theme)
    {
        $theme->delete();

        return redirect()->route('graveyard.obituary-background-themes.index')
            ->with('success', 'Background theme deleted successfully.');
    }

    /**
     * Toggle theme active status
     */
    public function toggleStatus(ObituaryBackgroundTheme $theme)
    {
        $theme->is_active = !$theme->is_active;
        $theme->save();

        $status = $theme->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Theme {$status} successfully.");
    }

    /**
     * Preview theme for frontend
     */
    public function preview(ObituaryBackgroundTheme $theme)
    {
        return response()->json([
            'theme' => $theme->toFrontendArray(),
            'style' => $theme->getCompleteStyleAttribute(),
        ]);
    }

    /**
     * Get themes for API (used by obituary forms)
     */
    public function api(Request $request)
    {
        $userTier = $request->get('tier', 'basic');

        $themes = $this->backgroundService->getThemesForFrontend($userTier);

        return response()->json([
            'themes' => $themes,
            'grouped' => $this->backgroundService->getThemesGroupedByTier(),
        ]);
    }
}
