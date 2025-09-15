<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Graveyard\Models\ObituaryPage;
use Modules\Graveyard\Models\ObituaryCondolence;
use Modules\Graveyard\Models\ObituaryPayment;
use App\Services\ObituaryService;
use App\Services\BackgroundService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Graveyard\Models\PermanentGraveBooking;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use Modules\Fund\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;


class ObituaryManagementController extends Controller
{
    protected $obituaryService;

    public function __construct(ObituaryService $obituaryService)
    {
        $this->obituaryService = $obituaryService;

        // Apply authorization middleware
        $this->middleware('auth');

        // Apply policy-based authorization
        $this->authorizeResource(ObituaryPage::class, 'obituary');
    }


    public function index(Request $request)
    {
        $obituaries = ObituaryPage::with(['permanentGraveBooking.validMember', 'temporaryGraveBooking'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('permanentGraveBooking.validMember', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('temporaryGraveBooking', function ($q) use ($search) {
                    $q->where('dead_first_name', 'like', "%{$search}%")
                        ->orWhere('dead_last_name', 'like', "%{$search}%");
                });
            })
            ->when($request->service_type, function ($query, $type) {
                $query->where('service_type', $type);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('PagesGraveyard/Obituaries/Index', [
            'obituaries' => $obituaries,
            'filters' => $request->only(['search', 'service_type']),
        ]);
    }

    public function create(Request $request)
    {
        $bookingType = $request->query('type'); // 'permanent' or 'temporary'
        $bookingId = $request->query('booking_id');

        $booking = null;
        if ($bookingType === 'permanent' && $bookingId) {
            $booking = PermanentGraveBooking::with('validMember')->findOrFail($bookingId);
        } elseif ($bookingType === 'temporary' && $bookingId) {
            $booking = TemporaryGraveBooking::findOrFail($bookingId);
        }

        return Inertia::render('PagesGraveyard/Obituaries/Create', [
            'booking' => $booking,
            'bookingType' => $bookingType,
            'basicBackgrounds' => BackgroundService::getBackgroundOptions('basic'),
            'premiumBackgrounds' => BackgroundService::getBackgroundOptions('premium'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_type' => 'required|in:permanent,temporary',
            'booking_id' => 'required|integer',
            'service_type' => 'required|in:basic,premium',
            'biography' => 'nullable|string',
            'favorite_memory' => 'nullable|string',
            'achievements' => 'nullable|string',
            'hobbies_interests' => 'nullable|string',
            'notes' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'nullable|image|max:2048',
            'audio_message' => 'nullable|file|mimes:mp3,wav,m4a|max:10240',
            'theme_color' => 'nullable|string|size:7',
            'background_style' => 'nullable|string',
            'allow_condolences' => 'boolean',
            'allow_memory_sharing' => 'boolean',
            'is_public' => 'boolean',
        ]);

        // Validate background style against service type
        if (isset($validated['background_style']) && $validated['background_style']) {
            if (!BackgroundService::isValidBackground($validated['background_style'], $validated['service_type'])) {
                return back()->withErrors(['background_style' => 'Selected background is not available for your service type.']);
            }
        }

        $booking = null;
        if ($validated['booking_type'] === 'permanent') {
            $booking = PermanentGraveBooking::findOrFail($validated['booking_id']);
        } else {
            $booking = TemporaryGraveBooking::findOrFail($validated['booking_id']);
        }

        // Handle file uploads
        $data = $validated;
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('obituaries/images', 'public');
        }

        if ($request->hasFile('gallery_images')) {
            $galleryImages = [];
            foreach ($request->file('gallery_images') as $image) {
                $galleryImages[] = $image->store('obituaries/gallery', 'public');
            }
            $data['gallery_images'] = $galleryImages;
        }

        if ($request->hasFile('audio_message')) {
            $data['audio_message'] = $request->file('audio_message')->store('obituaries/audio', 'public');
        }

        $obituary = $this->obituaryService->createObituaryFromBooking($booking, $data);

        // Create payment record for the obituary service
        $serviceAmount = $validated['service_type'] === 'premium' ? 1500 : 500;

        $payment = \Modules\Graveyard\Models\ObituaryPayment::create([
            'obituary_page_id' => $obituary->id,
            'service_type' => $validated['service_type'],
            'amount' => $serviceAmount,
            'payment_status' => 'pending',
            'payment_reference' => 'OBT' . date('Ymd') . str_pad($obituary->id, 4, '0', STR_PAD_LEFT),
            'created_by' => Auth::id(),
        ]);

        // Redirect to obituary show page with payment option
        return redirect()->route('graveyard.obituaries.show', $obituary)
            ->with('success', 'Obituary page created successfully! Please complete the payment to activate the page.')
            ->with('payment_required', true)
            ->with('payment_amount', $serviceAmount)
            ->with('service_type', $validated['service_type']);
    }

    public function show(ObituaryPage $obituary)
    {
        $obituary->load([
            'permanentGraveBooking.validMember',
            'temporaryGraveBooking',
            'condolences.obituaryPage',
            'payments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ]);

        $stats = $this->obituaryService->getObituaryStats($obituary);
        $recommendations = $this->obituaryService->getRecommendedUpgrades($obituary);
        $shareLinks = $this->obituaryService->generateShareableLink($obituary);
        $user = Auth::user();
        // Check if user can edit this obituary using policy
        $canEdit = Gate::allows('update', $obituary);

        // Get active payment methods
        $paymentMethods = PaymentMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->select('id', 'name', 'description')
            ->get();

        return Inertia::render('PagesGraveyard/Obituaries/Show', [
            'obituary' => $obituary,
            'stats' => $stats,
            'recommendations' => $recommendations,
            'shareLinks' => $shareLinks,
            'canEdit' => $canEdit,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function edit(ObituaryPage $obituary)
    {
        $obituary->load(['permanentGraveBooking.validMember', 'temporaryGraveBooking']);

        return Inertia::render('PagesGraveyard/Obituaries/Edit', [
            'obituary' => $obituary,
            'basicBackgrounds' => BackgroundService::getBackgroundOptions('basic'),
            'premiumBackgrounds' => BackgroundService::getBackgroundOptions('premium'),
        ]);
    }

    public function update(Request $request, ObituaryPage $obituary)
    {
        Log::info('Obituary update method called', [
            'obituary_id' => $obituary->id,
            'request_method' => $request->method(),
            'request_all' => $request->all(),
            'has_files' => [
                'profile_image' => $request->hasFile('profile_image'),
                'gallery_images' => $request->hasFile('gallery_images'),
                'audio_message' => $request->hasFile('audio_message')
            ]
        ]);

        try {
            $validated = $request->validate([
                'biography' => 'nullable|string',
                'favorite_memory' => 'nullable|string',
                'achievements' => 'nullable|string',
                'hobbies_interests' => 'nullable|string',
                'notes' => 'nullable|string',
                'profile_image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
                'gallery_images' => 'nullable|array',
                'gallery_images.*' => 'image|mimes:jpeg,jpg,png,gif|max:2048',
                'audio_message' => 'nullable|file|mimes:mp3,wav,m4a|max:10240',
                'theme_color' => 'nullable|string',
                'background_style' => 'nullable|string',
                'allow_condolences' => 'nullable|boolean',
                'allow_memory_sharing' => 'nullable|boolean',
                'is_public' => 'nullable|boolean',
            ]);

            // Validate background style against service type
            if (isset($validated['background_style']) && $validated['background_style']) {
                if (!BackgroundService::isValidBackground($validated['background_style'], $obituary->service_type)) {
                    return back()->withErrors(['background_style' => 'Selected background is not available for your service type.']);
                }
            }

            Log::info('Obituary update - validation passed', [
                'obituary_id' => $obituary->id,
                'validated_data' => $validated,
                'request_all' => $request->all()
            ]);

            // Prepare data for update
            $data = [];

            // Text fields
            $textFields = ['biography', 'favorite_memory', 'achievements', 'hobbies_interests', 'notes', 'theme_color', 'background_style'];
            foreach ($textFields as $field) {
                if (isset($validated[$field])) {
                    $data[$field] = $validated[$field];
                }
            }

            // Boolean fields - handle form data properly
            $booleanFields = ['allow_condolences', 'allow_memory_sharing', 'is_public'];
            foreach ($booleanFields as $field) {
                if ($request->has($field)) {
                    $data[$field] = filter_var($request->input($field), FILTER_VALIDATE_BOOLEAN);
                }
            }

            // Handle profile image upload
            if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
                $data['profile_image'] = $request->file('profile_image')->store('obituaries/images', 'public');
            }

            // Handle gallery images upload
            if ($request->hasFile('gallery_images')) {
                $existingImages = $obituary->gallery_images ?? [];
                $newImages = [];
                foreach ($request->file('gallery_images') as $image) {
                    if ($image->isValid()) {
                        $newImages[] = $image->store('obituaries/gallery', 'public');
                    }
                }
                if (!empty($newImages)) {
                    $data['gallery_images'] = array_merge($existingImages, $newImages);
                }
            }

            // Handle audio message upload
            if ($request->hasFile('audio_message') && $request->file('audio_message')->isValid()) {
                $data['audio_message'] = $request->file('audio_message')->store('obituaries/audio', 'public');
            }

            // Update the obituary
            $obituary->update($data);

            return redirect()->route('graveyard.obituaries.show', $obituary->uuid)
                ->with('success', 'Obituary page updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Obituary update failed: ' . $e->getMessage(), [
                'obituary_id' => $obituary->id,
                'request_data' => $request->all()
            ]);

            return back()->with('error', 'Failed to update obituary. Please try again.');
        }
    }

    public function destroy(ObituaryPage $obituary)
    {
        $obituary->delete();

        return redirect()->route('graveyard.obituaries.index')
            ->with('success', 'Obituary page deleted successfully!');
    }

    public function condolences(Request $request)
    {
        // Check policy authorization for managing condolences
        $this->authorize('manageCondolences', ObituaryPage::class);

        $condolences = ObituaryCondolence::with('obituaryPage')
            ->when($request->status, function ($query, $status) {
                if ($status === 'pending') {
                    $query->where('is_approved', false);
                } elseif ($status === 'approved') {
                    $query->where('is_approved', true);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('PagesGraveyard/Obituaries/Condolences', [
            'condolences' => $condolences,
            'filters' => $request->only(['status']),
        ]);
    }

    public function approveCondolence(ObituaryCondolence $condolence)
    {
        // Check policy authorization for approving condolences
        $this->authorize('approveCondolences', ObituaryPage::class);

        $condolence->approve();

        return back()->with('success', 'Condolence approved successfully!');
    }

    public function rejectCondolence(ObituaryCondolence $condolence)
    {
        // Check policy authorization for rejecting condolences
        $this->authorize('rejectCondolences', ObituaryPage::class);

        $condolence->reject();

        return back()->with('success', 'Condolence rejected successfully!');
    }

    public function generateCustomQr(Request $request, ObituaryPage $obituary)
    {
        // Check policy authorization for generating QR codes
        $this->authorize('generateQrCode', $obituary);

        $validated = $request->validate([
            'size' => 'nullable|integer|min:100|max:1000',
            'margin' => 'nullable|integer|min:0|max:10',
            'color' => 'nullable|array',
            'color.r' => 'required_with:color|integer|min:0|max:255',
            'color.g' => 'required_with:color|integer|min:0|max:255',
            'color.b' => 'required_with:color|integer|min:0|max:255',
            'background_color' => 'nullable|array',
            'background_color.r' => 'required_with:background_color|integer|min:0|max:255',
            'background_color.g' => 'required_with:background_color|integer|min:0|max:255',
            'background_color.b' => 'required_with:background_color|integer|min:0|max:255',
        ]);

        // If no custom options provided, generate standard QR code
        if (empty(array_filter($validated))) {
            $qrCodeUrl = $this->obituaryService->generateQrCode($obituary);
            return back()->with('success', 'Standard QR code generated successfully!');
        }

        // Generate custom QR code with provided options
        $qrCodeUrl = $this->obituaryService->createCustomQrCode($obituary, $validated);

        if ($request->expectsJson()) {
            return response()->json(['qr_code_url' => $qrCodeUrl]);
        }

        return back()->with('success', 'Custom QR code generated successfully!');
    }

    public function processPayment(Request $request, ObituaryPage $obituary)
    {
        // Check policy authorization for processing payments
        $this->authorize('processPayments', ObituaryPage::class);

        $validated = $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Find the pending payment record
        $payment = \Modules\Graveyard\Models\ObituaryPayment::where('obituary_page_id', $obituary->id)
            ->where('payment_status', 'pending')
            ->first();

        if (!$payment) {
            return back()->with('error', 'No pending payment found for this obituary.');
        }

        // Get payment method name
        $paymentMethod = PaymentMethod::find($validated['payment_method_id']);

        // Update payment record
        $payment->update([
            'payment_status' => 'completed',
            'payment_method_id' => $validated['payment_method_id'],
            'payment_method' => $paymentMethod->name, // Keep the string field for compatibility
            'paid_amount' => $validated['amount'],
            'payment_date' => now(),
            'notes' => $validated['notes'],
            'updated_by' => Auth::id(),
        ]);

        // Activate the obituary page
        $obituary->update(['is_active' => true]);

        return back()->with('success', 'Payment completed successfully! The obituary page is now active.');
    }

    public function downloadQrCode(ObituaryPage $obituary)
    {
        // Check policy authorization for downloading QR codes
        $this->authorize('view', $obituary);

        // If no QR code path or file doesn't exist, generate it automatically
        if (!$obituary->qr_code_path || !Storage::disk('public')->exists($obituary->qr_code_path)) {
            $this->obituaryService->generateQrCode($obituary);
            $obituary->refresh(); // Reload to get updated qr_code_path
        }

        // Final check - if still no QR code, return error
        if (!$obituary->qr_code_path || !Storage::disk('public')->exists($obituary->qr_code_path)) {
            return back()->with('error', 'Failed to generate QR code. Please try again.');
        }

        // Increment QR scan count
        $obituary->increment('qr_scan_count');

        // Get file path and return file directly to avoid output buffer issues
        $filePath = Storage::disk('public')->path($obituary->qr_code_path);
        $fileName = 'obituary-qr-' . $obituary->uuid . '.png';

        // Ensure file exists at the path
        if (!file_exists($filePath)) {
            return back()->with('error', 'QR code file not found on disk. Please regenerate the QR code.');
        }

        // Clean any output buffers to prevent corruption
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Return file directly using Laravel's download helper
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'image/png',
        ]);
    }

    public function removeProfileImage(ObituaryPage $obituary)
    {
        // Check policy authorization for updating obituaries
        $this->authorize('update', $obituary);

        if ($obituary->profile_image) {
            // Delete the file from storage
            if (Storage::disk('public')->exists($obituary->profile_image)) {
                Storage::disk('public')->delete($obituary->profile_image);
            }

            // Update the database
            $obituary->update(['profile_image' => null]);

            return back()->with('success', 'Profile image removed successfully!');
        }

        return back()->with('error', 'No profile image to remove.');
    }

    public function removeGalleryImage(Request $request, ObituaryPage $obituary)
    {
        // Check policy authorization for updating obituaries
        $this->authorize('update', $obituary);

        $validated = $request->validate([
            'image_path' => 'required|string'
        ]);

        $imagePath = $validated['image_path'];
        $galleryImages = $obituary->gallery_images ?? [];

        // Check if image exists in gallery
        $imageIndex = array_search($imagePath, $galleryImages);
        if ($imageIndex === false) {
            return back()->with('error', 'Image not found in gallery.');
        }

        // Delete the file from storage
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }

        // Remove from gallery array
        unset($galleryImages[$imageIndex]);
        $galleryImages = array_values($galleryImages); // Re-index array

        // Update the database
        $obituary->update(['gallery_images' => $galleryImages]);

        return back()->with('success', 'Gallery image removed successfully!');
    }

    public function removeAudioMessage(ObituaryPage $obituary)
    {
        // Check policy authorization for updating obituaries
        $this->authorize('update', $obituary);

        if ($obituary->audio_message) {
            // Delete the file from storage
            if (Storage::disk('public')->exists($obituary->audio_message)) {
                Storage::disk('public')->delete($obituary->audio_message);
            }

            // Update the database
            $obituary->update(['audio_message' => null]);

            return back()->with('success', 'Audio message removed successfully!');
        }

        return back()->with('error', 'No audio message to remove.');
    }

    public function cleanupOrphanedFiles(ObituaryPage $obituary)
    {
        // Check policy authorization for managing obituaries (admin only)
        $this->authorize('manageFiles', ObituaryPage::class);

        $deletedFiles = [];
        $obituaryFolders = ['obituaries/images', 'obituaries/gallery', 'obituaries/audio'];

        foreach ($obituaryFolders as $folder) {
            if (!Storage::disk('public')->exists($folder)) continue;

            $files = Storage::disk('public')->files($folder);

            foreach ($files as $file) {
                $isReferenced = ObituaryPage::where('profile_image', $file)
                    ->orWhere('audio_message', $file)
                    ->orWhereJsonContains('gallery_images', $file)
                    ->exists();

                if (!$isReferenced) {
                    Storage::disk('public')->delete($file);
                    $deletedFiles[] = $file;
                }
            }
        }

        $count = count($deletedFiles);
        return back()->with('success', "Cleaned up {$count} orphaned files.");
    }

    public function extendExpiration(Request $request, ObituaryPage $obituary)
    {
        // Check policy authorization for updating obituaries
        $this->authorize('update', $obituary);

        $validated = $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $currentExpiry = $obituary->expires_at ?? now();
        $newExpiry = \Carbon\Carbon::parse($currentExpiry)->addDays($validated['days']);

        $obituary->update(['expires_at' => $newExpiry]);

        return back()->with('success', "Expiration extended by {$validated['days']} days to {$newExpiry->format('M j, Y')}");
    }

    public function publish(ObituaryPage $obituary)
    {
        // Check if user has permission to publish obituary pages
        if (!auth()->user()->can('publish-obituary-page')) {
            abort(403, 'You do not have permission to publish obituary pages.');
        }

        // Check policy authorization for publishing obituaries
        $this->authorize('update', $obituary);

        if ($obituary->isPublished()) {
            return back()->with('error', 'This obituary page is already published.');
        }

        $obituary->publish(auth()->id());

        return back()->with('success', 'Obituary page has been published successfully.');
    }

    public function unpublish(ObituaryPage $obituary)
    {
        // Check if user has permission to unpublish obituary pages
        if (!auth()->user()->can('unpublish-obituary-page')) {
            abort(403, 'You do not have permission to unpublish obituary pages.');
        }

        // Check policy authorization for updating obituaries
        $this->authorize('update', $obituary);

        if (!$obituary->isPublished()) {
            return back()->with('error', 'This obituary page is not published.');
        }

        $obituary->unpublish();

        return back()->with('success', 'Obituary page has been unpublished successfully.');
    }
}
