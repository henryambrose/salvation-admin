<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Graveyard\Models\ObituaryManager;
use Modules\Graveyard\Models\ObituaryPage;
use Modules\Graveyard\Models\ObituaryCondolence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ObituaryManagerController extends Controller
{
    /**
     * Display a listing of obituary managers (Admin)
     */
    public function index()
    {
        $obituaryManagers = ObituaryManager::with([
            'obituaryPage.permanentGraveBooking.validMember',
            'obituaryPage.temporaryGraveBooking',
            'accessGrantedBy'
        ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('PagesGraveyard/ObituaryManagers/Index', [
            'obituaryManagers' => $obituaryManagers,
        ]);
    }

    /**
     * Show the form for creating a new obituary manager (Admin)
     */
    public function create()
    {
        $obituaryPages = ObituaryPage::whereDoesntHave('obituaryManager')
            ->with(['permanentGraveBooking.validMember', 'temporaryGraveBooking'])
            ->get()
            ->map(function ($obituary) {
                $deceasedName = $this->getDeceasedName($obituary);
                return [
                    'id' => $obituary->id,
                    'uuid' => $obituary->uuid,
                    'deceased_name' => $deceasedName,
                ];
            });

        return Inertia::render('PagesGraveyard/ObituaryManagers/Create', [
            'obituaryPages' => $obituaryPages,
        ]);
    }

    /**
     * Store a newly created obituary manager (Admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'obituary_page_id' => 'required|exists:obituary_pages,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:obituary_managers,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        ObituaryManager::create([
            'obituary_page_id' => $request->obituary_page_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'access_granted_by' => Auth::id(),
            'access_granted_at' => now(),
        ]);

        return redirect()->route('graveyard.obituary-managers.index')
            ->with('success', 'Obituary manager created successfully.');
    }

    /**
     * Display the specified obituary manager (Admin)
     */
    public function show(ObituaryManager $obituaryManager)
    {
        $obituaryManager->load([
            'obituaryPage.permanentGraveBooking.validMember',
            'obituaryPage.temporaryGraveBooking',
            'accessGrantedBy'
        ]);

        return Inertia::render('PagesGraveyard/ObituaryManagers/Show', [
            'obituaryManager' => $obituaryManager,
            'deceasedName' => $this->getDeceasedName($obituaryManager->obituaryPage),
        ]);
    }

    /**
     * Show the form for editing the specified obituary manager (Admin)
     */
    public function edit(ObituaryManager $obituaryManager)
    {
        $obituaryManager->load([
            'obituaryPage.permanentGraveBooking.validMember',
            'obituaryPage.temporaryGraveBooking'
        ]);

        return Inertia::render('PagesGraveyard/ObituaryManagers/Edit', [
            'obituaryManager' => $obituaryManager,
            'deceasedName' => $this->getDeceasedName($obituaryManager->obituaryPage),
        ]);
    }

    /**
     * Update the specified obituary manager (Admin)
     */
    public function update(Request $request, ObituaryManager $obituaryManager)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:obituary_managers,email,' . $obituaryManager->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $obituaryManager->update($data);

        return redirect()->route('graveyard.obituary-managers.index')
            ->with('success', 'Obituary manager updated successfully.');
    }

    /**
     * Remove the specified obituary manager (Admin)
     */
    public function destroy(ObituaryManager $obituaryManager)
    {
        $obituaryManager->delete();

        return redirect()->route('graveyard.obituary-managers.index')
            ->with('success', 'Obituary manager deleted successfully.');
    }

    /**
     * Toggle active status of obituary manager (Admin)
     */
    public function toggleActive(ObituaryManager $obituaryManager)
    {
        $obituaryManager->update([
            'is_active' => !$obituaryManager->is_active,
        ]);

        $status = $obituaryManager->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "Obituary manager {$status} successfully.",
            'is_active' => $obituaryManager->is_active,
        ]);
    }

    /**
     * Show login form for obituary managers
     */
    public function showLogin(string $uuid)
    {
        $obituary = ObituaryPage::where('uuid', $uuid)
            ->with([
                'obituaryManager',
                'permanentGraveBooking.validMember',
                'temporaryGraveBooking'
            ])
            ->first();

        if (!$obituary) {
            abort(404, 'Obituary page not found');
        }

        // Check if obituary manager access exists
        if (!$obituary->obituaryManager || !$obituary->obituaryManager->is_active) {
            abort(403, 'Management access not available for this obituary');
        }

        return Inertia::render('Public/Obituary/ExternalLogin', [
            'obituary' => $obituary,
            'deceasedName' => $this->getDeceasedName($obituary),
        ]);
    }

    /**
     * Handle obituary manager login
     */
    public function login(Request $request, string $uuid)
    {
        $obituary = ObituaryPage::where('uuid', $uuid)
            ->with('obituaryManager')
            ->first();

        if (!$obituary || !$obituary->obituaryManager) {
            return response()->json(['error' => 'Access not available'], 404);
        }

        $obituaryManager = $obituary->obituaryManager;

        // Check if member is active and not blocked
        if (!$obituaryManager->canAccess()) {
            $message = $obituaryManager->blocked_until && $obituaryManager->blocked_until->isFuture()
                ? 'Account is temporarily blocked. Please try again later.'
                : 'Access has been disabled by the administrator.';

            return response()->json(['error' => $message], 403);
        }

        // Rate limiting
        $key = 'external-login:' . $request->ip() . ':' . $uuid;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'error' => "Too many login attempts. Please try again in {$seconds} seconds."
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Please provide valid email and password',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check credentials
        if ($obituaryManager->email !== $request->email || !Hash::check($request->password, $obituaryManager->password)) {
            RateLimiter::hit($key, 300); // 5 minutes
            $obituaryManager->incrementLoginAttempts();

            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Successful login
        RateLimiter::clear($key);
        $obituaryManager->resetLoginAttempts();

        // Create session for obituary manager
        Auth::guard('external')->login($obituaryManager);

        return response()->json([
            'success' => true,
            'redirect' => route('obituary.external.dashboard', $uuid)
        ]);
    }

    /**
     * Show obituary manager dashboard
     */
    public function dashboard(string $uuid)
    {
        $obituaryManager = Auth::guard('external')->user();

        if (!$obituaryManager || $obituaryManager->obituaryPage->uuid !== $uuid) {
            return redirect()->route('obituary.external.login', $uuid);
        }

        $obituary = $obituaryManager->obituaryPage
            ->load([
                'permanentGraveBooking.validMember',
                'temporaryGraveBooking',
                'condolences' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }
            ]);

        $stats = [
            'total_condolences' => $obituary->condolences()->count(),
            'pending_condolences' => $obituary->condolences()->pending()->count(),
            'approved_condolences' => $obituary->condolences()->approved()->count(),
            'rejected_condolences' => $obituary->condolences()->rejected()->count(),
            'total_views' => $obituary->view_count ?? 0,
            'qr_scans' => $obituary->qr_scan_count ?? 0,
        ];

        return Inertia::render('Public/Obituary/ExternalDashboard', [
            'obituary' => $obituary,
            'deceasedName' => $this->getDeceasedName($obituary),
            'stats' => $stats,
            'recentCondolences' => $obituary->condolences()->take(5)->get(),
        ]);
    }

    /**
     * Show obituary edit form for obituary manager
     */
    public function editObituary(string $uuid)
    {
        $obituaryManager = Auth::guard('external')->user();

        if (!$obituaryManager || $obituaryManager->obituaryPage->uuid !== $uuid) {
            return redirect()->route('obituary.external.login', $uuid);
        }

        $obituary = $obituaryManager->obituaryPage;

        // Check if obituary is published
        if (!$obituary->is_published) {
            return redirect()->route('obituary.external.dashboard', $uuid)
                ->with('error', 'Obituary content can only be edited after it has been published.');
        }

        // Get background options for premium users
        $backgroundOptions = [];
        // if ($obituary->service_type === 'premium') {
        $themes = \Modules\Graveyard\Models\ObituaryBackgroundTheme::where('is_active', true)
            ->orderBy('sort_order')
            ->get();


        $backgroundOptions = $themes->map(function ($theme) {
            $imageUrl = $theme->image_path ? asset($theme->image_path) : null;

            return [
                'value' => $theme->key,
                'label' => $theme->name,
                'description' => $theme->description,
                'image' => $imageUrl,
                'tier' => $theme->tier,
            ];
        })
            ->toArray();
        // }

        return Inertia::render('Public/Obituary/ExternalEdit', [
            'obituary' => $obituary,
            'deceasedName' => $this->getDeceasedName($obituary),
            'backgroundOptions' => $backgroundOptions,
        ]);
    }

    /**
     * Update obituary content
     */
    public function updateObituary(Request $request, string $uuid)
    {
        $obituaryManager = Auth::guard('external')->user();

        if (!$obituaryManager || $obituaryManager->obituaryPage->uuid !== $uuid) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $obituary = $obituaryManager->obituaryPage;

        // Check if obituary is published
        if (!$obituary->is_published) {
            return response()->json(['error' => 'Obituary content can only be updated after it has been published.'], 403);
        }

        $rules = [
            'biography' => 'nullable|string|max:2000',
            'favorite_memory' => 'nullable|string|max:1000',
            'achievements' => 'nullable|string|max:1000',
            'hobbies_interests' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'is_public' => 'nullable|in:0,1,true,false',
            'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB
        ];

        // Add premium-only validation rules
        if ($obituary->service_type === 'premium') {
            $rules = array_merge($rules, [
                'theme_color' => 'nullable|string|regex:/^#[a-fA-F0-9]{6}$/',
                'background_style' => 'nullable|string|max:50',
                'allow_condolences' => 'nullable|in:0,1,true,false',
                'allow_memory_sharing' => 'nullable|in:0,1,true,false',
                'gallery_images.*' => 'image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB each
                'audio_message' => 'nullable|file|mimes:mp3,wav,m4a,aac|max:25600', // 25MB
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $validator->validated();

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($obituary->profile_image) {
                Storage::disk('public')->delete($obituary->profile_image);
            }

            $profilePath = $request->file('profile_image')->store('obituaries/profiles', 'public');
            $updateData['profile_image'] = $profilePath;
        }

        // Handle gallery images (premium only)
        if ($obituary->service_type === 'premium') {
            $existingGallery = $obituary->gallery_images ?? [];

            // Handle image removal
            if ($request->has('remove_gallery_images')) {
                $imagesToRemove = json_decode($request->get('remove_gallery_images'), true);

                // Remove files from storage
                foreach ($imagesToRemove as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }

                // Remove from existing gallery array
                $existingGallery = array_filter($existingGallery, function ($image) use ($imagesToRemove) {
                    return !in_array($image, $imagesToRemove);
                });

                // Re-index array to avoid gaps
                $existingGallery = array_values($existingGallery);
            }

            // Handle new image uploads
            if ($request->hasFile('gallery_images')) {
                $galleryPaths = [];

                foreach ($request->file('gallery_images') as $file) {
                    $galleryPath = $file->store('obituaries/gallery', 'public');
                    $galleryPaths[] = $galleryPath;
                }

                // Merge with existing gallery images
                $existingGallery = array_merge($existingGallery, $galleryPaths);
            }

            // Always update gallery_images field if there were any changes
            if ($request->has('remove_gallery_images') || $request->hasFile('gallery_images')) {
                $updateData['gallery_images'] = $existingGallery;
            }
        }

        // Handle audio message upload (premium only)
        if ($obituary->service_type === 'premium' && $request->hasFile('audio_message')) {
            // Delete old audio message if exists
            if ($obituary->audio_message) {
                Storage::disk('public')->delete($obituary->audio_message);
            }

            $audioPath = $request->file('audio_message')->store('obituaries/audio', 'public');
            $updateData['audio_message'] = $audioPath;
        }

        // Convert boolean strings to actual booleans for database storage
        if (isset($updateData['is_public'])) {
            $updateData['is_public'] = in_array($updateData['is_public'], ['1', 'true', true], true);
        }

        if ($obituary->service_type === 'premium') {
            if (isset($updateData['allow_condolences'])) {
                $updateData['allow_condolences'] = in_array($updateData['allow_condolences'], ['1', 'true', true], true);
            }
            if (isset($updateData['allow_memory_sharing'])) {
                $updateData['allow_memory_sharing'] = in_array($updateData['allow_memory_sharing'], ['1', 'true', true], true);
            }
        }

        $obituary->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Obituary updated successfully'
        ]);
    }

    /**
     * Show condolences management
     */
    public function condolences(string $uuid)
    {
        $obituaryManager = Auth::guard('external')->user();

        if (!$obituaryManager || $obituaryManager->obituaryPage->uuid !== $uuid) {
            return redirect()->route('obituary.external.login', $uuid);
        }

        $obituary = $obituaryManager->obituaryPage;

        // Check if obituary is published
        if (!$obituary->is_published) {
            return redirect()->route('obituary.external.dashboard', $uuid)
                ->with('error', 'Condolences can only be managed after the obituary has been published.');
        }

        $condolences = $obituary->condolences()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Public/Obituary/ExternalCondolences', [
            'obituary' => $obituary,
            'deceasedName' => $this->getDeceasedName($obituary),
            'condolences' => $condolences,
        ]);
    }

    /**
     * Approve condolence
     */
    public function approveCondolence(Request $request, string $uuid, ObituaryCondolence $condolence)
    {
        $obituaryManager = Auth::guard('external')->user();

        if (!$obituaryManager || $obituaryManager->obituaryPage->uuid !== $uuid || $condolence->obituary_page_id !== $obituaryManager->obituaryPage->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if obituary is published
        if (!$obituaryManager->obituaryPage->is_published) {
            return response()->json(['error' => 'Condolences can only be managed after the obituary has been published.'], 403);
        }

        $condolence->approve();

        return response()->json([
            'success' => true,
            'message' => 'Condolence approved successfully'
        ]);
    }

    /**
     * Reject condolence
     */
    public function rejectCondolence(Request $request, string $uuid, ObituaryCondolence $condolence)
    {
        $obituaryManager = Auth::guard('external')->user();

        if (!$obituaryManager || $obituaryManager->obituaryPage->uuid !== $uuid || $condolence->obituary_page_id !== $obituaryManager->obituaryPage->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if obituary is published
        if (!$obituaryManager->obituaryPage->is_published) {
            return response()->json(['error' => 'Condolences can only be managed after the obituary has been published.'], 403);
        }

        $condolence->reject();

        return response()->json([
            'success' => true,
            'message' => 'Condolence rejected successfully'
        ]);
    }

    /**
     * Obituary manager logout
     */
    public function logout(string $uuid)
    {
        Auth::guard('external')->logout();

        return redirect()->route('obituary.show', $uuid)
            ->with('message', 'Logged out successfully');
    }

    /**
     * Get deceased person's name from obituary
     */
    private function getDeceasedName(ObituaryPage $obituary): string
    {
        if ($obituary->permanentGraveBooking) {
            $member = $obituary->permanentGraveBooking->validMember;
            return trim($member->first_name . ' ' . $member->last_name);
        }

        if ($obituary->temporaryGraveBooking) {
            $booking = $obituary->temporaryGraveBooking;
            return trim($booking->dead_first_name . ' ' . $booking->dead_last_name);
        }

        return 'Unknown';
    }
}
