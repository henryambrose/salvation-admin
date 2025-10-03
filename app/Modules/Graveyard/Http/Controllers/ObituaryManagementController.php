<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Graveyard\Models\ObituaryPage;
use Modules\Graveyard\Models\ObituaryCondolence;
use Modules\Graveyard\Models\ObituaryBackgroundTheme;
use Modules\Graveyard\Services\ObituaryService;
use Modules\Graveyard\Services\BackgroundService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Graveyard\Models\PermanentGraveBooking;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use Modules\Fund\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class ObituaryManagementController extends Controller
{
    protected $obituaryService;

    public function __construct(ObituaryService $obituaryService)
    {
        $this->obituaryService = $obituaryService;

        // Apply authorization middleware
        $this->middleware('auth');

        // Temporarily disabled to debug authentication flow
        // $this->authorizeResource(ObituaryPage::class, 'obituary', [
        //     'except' => ['rephraseText']
        // ]);
    }


    public function index(Request $request)
    {
        $obituaries = ObituaryPage::with(['permanentGraveBooking.validMember', 'temporaryGraveBooking', 'obituaryPlan'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('permanentGraveBooking.validMember', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('temporaryGraveBooking', function ($q) use ($search) {
                    $q->where('dead_first_name', 'like', "%{$search}%")
                        ->orWhere('dead_last_name', 'like', "%{$search}%");
                });
            })
            ->when($request->obituary_plan_id, function ($query, $planId) {
                $query->where('obituary_plan_id', $planId);
            })
            ->when($request->payment_status, function ($query, $status) {
                if ($status === 'paid') {
                    $query->whereHas('permanentGraveBooking', function ($q) {
                        $q->whereIn('payment_status', ['paid', 'completed']);
                    })->orWhereHas('temporaryGraveBooking', function ($q) {
                        $q->whereIn('payment_status', ['paid', 'completed']);
                    });
                } elseif ($status === 'pending') {
                    $query->whereHas('permanentGraveBooking', function ($q) {
                        $q->where('payment_status', 'pending');
                    })->orWhereHas('temporaryGraveBooking', function ($q) {
                        $q->where('payment_status', 'pending');
                    });
                } elseif ($status === 'partial') {
                    $query->whereHas('permanentGraveBooking', function ($q) {
                        $q->where('payment_status', 'partial');
                    })->orWhereHas('temporaryGraveBooking', function ($q) {
                        $q->where('payment_status', 'partial');
                    });
                }
            })
            ->when($request->published_status, function ($query, $status) {
                if ($status === 'published') {
                    $query->where('is_published', true);
                } elseif ($status === 'draft') {
                    $query->where('is_published', false);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Add payment status to each obituary
        $obituaries->getCollection()->transform(function ($obituary) {
            $obituary->payment_status = $obituary->getPaymentStatus();
            $obituary->can_be_published = $obituary->canBePublished();
            $obituary->can_be_accessed_publicly = $obituary->canBeAccessedPublicly();
            return $obituary;
        });

        // Get all active obituary plans for filter dropdown
        $obituaryPlans = \Modules\Graveyard\Models\ObituaryPlan::where('is_active', true)
            ->orderBy('cost', 'asc')
            ->get(['id', 'name', 'cost']);

        return Inertia::render('PagesGraveyard/Obituaries/Index', [
            'obituaries' => $obituaries,
            'filters' => $request->only(['search', 'obituary_plan_id', 'payment_status', 'published_status']),
            'obituaryPlans' => $obituaryPlans,
        ]);
    }

    public function create(Request $request)
    {
        // Debug logging
        Log::info('ObituaryManagementController::create - START', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'is_authenticated' => Auth::check(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        $bookingType = $request->query('type'); // 'permanent' or 'temporary'
        $bookingId = $request->query('booking_id');

        $booking = null;
        if ($bookingType === 'permanent' && $bookingId) {
            $booking = PermanentGraveBooking::with('validMember')->findOrFail($bookingId);
        } elseif ($bookingType === 'temporary' && $bookingId) {
            $booking = TemporaryGraveBooking::findOrFail($bookingId);
        }

        // Get available obituary plans
        $obituaryPlans = \Modules\Graveyard\Models\ObituaryPlan::active()->ordered()->get();

        Log::info('ObituaryManagementController::create - RENDERING', [
            'booking_type' => $bookingType,
            'booking_id' => $bookingId,
            'has_booking' => !is_null($booking),
            'obituary_plans_count' => $obituaryPlans->count(),
        ]);

        return Inertia::render('PagesGraveyard/Obituaries/Create', [
            'booking' => $booking,
            'bookingType' => $bookingType,
            'obituaryPlans' => $obituaryPlans,
            'backgrounds' => ObituaryBackgroundTheme::active()->ordered()->get()->map(function ($theme) {
                return $theme->toFrontendArray();
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_type' => 'required|in:permanent,temporary',
            'booking_id' => 'required|integer',
            'obituary_plan_id' => 'required|integer|exists:obituary_plans,id',
            // 'service_type' => 'required|in:basic,premium', // Keep for compatibility
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
        // if (isset($validated['background_style']) && $validated['background_style']) {
        //     if (!BackgroundService::isValidBackground($validated['background_style'], $validated['service_type'])) {
        //         return back()->withErrors(['background_style' => 'Selected background is not available for your service type.']);
        //     }
        // }

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

        // Get the selected obituary plan
        $obituaryPlan = \Modules\Graveyard\Models\ObituaryPlan::findOrFail($validated['obituary_plan_id']);

        // Add obituary_plan_id to data
        $data['obituary_plan_id'] = $obituaryPlan->id;

        $obituary = $this->obituaryService->createObituaryFromBooking($booking, $data);

        // Create payment record for the obituary service using plan pricing
        $payment = \Modules\Graveyard\Models\ObituaryPayment::create([
            'obituary_page_id' => $obituary->id,
            'obituary_plan_id' => $obituaryPlan->id,
            'amount' => $obituaryPlan->cost,
            'payment_status' => 'pending',
            'payment_reference' => 'OBT' . date('Ymd') . str_pad($obituary->id, 4, '0', STR_PAD_LEFT),
            'created_by' => Auth::id(),
            'expires_at' => $obituaryPlan->isLifetime() ? null : now()->addDays($obituaryPlan->duration_in_days),
        ]);

        // Redirect to obituary show page with payment option
        return redirect()->route('graveyard.obituaries.show', $obituary)
            ->with('success', 'Obituary page created successfully! Please complete the payment to activate the page.')
            ->with('payment_required', true)
            ->with('payment_amount', $obituaryPlan->cost)
            ->with('service_type', $obituaryPlan->name) // Use plan name instead of deprecated service_type
            ->with('plan_name', $obituaryPlan->name);
    }

    public function show(ObituaryPage $obituary)
    {
        $obituary->load([
            'permanentGraveBooking.validMember',
            'temporaryGraveBooking',
            'condolences.obituaryPage',
            'payments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'obituaryManager'
        ]);

        $stats = $this->obituaryService->getObituaryStats($obituary);
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
            'backgrounds' => ObituaryBackgroundTheme::active()->ordered()->get()->map(function ($theme) {
                return $theme->toFrontendArray();
            }),
        ]);
    }

    public function update(Request $request, ObituaryPage $obituary)
    {

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
            // if (isset($validated['background_style']) && $validated['background_style']) {
            //     if (!BackgroundService::isValidBackground($validated['background_style'], $obituary->service_type)) {
            //         return back()->withErrors(['background_style' => 'Selected background is not available for your service type.']);
            //     }
            // }

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

            // Check if it's an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Obituary updated successfully!'
                ]);
            }

            return redirect()->route('graveyard.obituaries.show', $obituary->uuid)
                ->with('success', 'Obituary updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Obituary update failed: ' . $e->getMessage(), [
                'obituary_id' => $obituary->id,
                'request_data' => $request->all()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update obituary. Please try again.'
                ], 500);
            }

            return back()->with('error', 'Failed to update obituary. Please try again.');
        }
    }

    public function destroy(ObituaryPage $obituary)
    {
        $obituary->delete();

        return redirect()->route('graveyard.obituaries.index')
            ->with('success', 'Obituary page deleted successfully!');
    }

    public function restore($uuid)
    {
        $obituary = ObituaryPage::withTrashed()->where('uuid', $uuid)->firstOrFail();

        // Check policy authorization for restoring obituaries
        $this->authorize('restore', $obituary);

        $obituary->restore();

        return back()->with('success', 'Obituary page restored successfully!');
    }

    public function condolences(Request $request)
    {
        // Check policy authorization for managing condolences
        $this->authorize('manageCondolences', ObituaryPage::class);

        $condolences = ObituaryCondolence::with([
            'obituaryPage.permanentGraveBooking.validMember',
            'obituaryPage.temporaryGraveBooking'
        ])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('visitor_name', 'like', "%{$search}%")
                        ->orWhere('visitor_email', 'like', "%{$search}%")
                        ->orWhere('visitor_phone', 'like', "%{$search}%")
                        ->orWhere('visitor_ip', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%")
                        ->orWhereHas('obituaryPage.permanentGraveBooking.validMember', function ($q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('obituaryPage.temporaryGraveBooking', function ($q) use ($search) {
                            $q->where('dead_first_name', 'like', "%{$search}%")
                                ->orWhere('dead_last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'pending') {
                    $query->where('is_approved', false)->where('is_rejected', false);
                } elseif ($status === 'approved') {
                    $query->where('is_approved', true);
                } elseif ($status === 'rejected') {
                    $query->where('is_rejected', true);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('PagesGraveyard/Obituaries/Condolences', [
            'condolences' => $condolences,
            'filters' => $request->only(['search', 'status']),
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

        // Check if user is super admin or has permission to process payments
        if (!Auth::user()->is_superadmin && !Gate::allows('update', $obituary)) {
            abort(403, 'You do not have permission to process payments for obituary pages.');
        }

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
            'paid_amount' => $validated['amount'],
            'payment_date' => now(),
            'notes' => $validated['notes'],
            'updated_by' => Auth::id(),
        ]);

        // Activate the obituary page
        $obituary->update(['is_active' => true]);

        return back()->with('success', 'Payment completed successfully! The obituary page is now active.');
    }

    public function downloadQrCode(ObituaryPage $obituary): BinaryFileResponse
    {
        $this->authorize('view', $obituary);

        // normalize to disk-relative path like "qr-codes/obituary-<uuid>.png"
        $path = ltrim(str_replace(['public/', '\\'], ['', '/'], (string) $obituary->qr_code_path), '/');
        abort_unless($path && Storage::disk('public')->exists($path), 404, 'QR code file not found');

        // (optional) disable debugbar for this response, just in case
        if (app()->bound('debugbar')) {
            app('debugbar')->disable();
        }

        $downloadName = 'obituary-' . $obituary->uuid . '.png';

        // Let Laravel stream the file; don't set Content-Length manually.
        return Storage::disk('public')->download($path, $downloadName, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
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

    public function removeGalleryImagesBulk(Request $request, ObituaryPage $obituary)
    {
        // Check policy authorization for updating obituaries
        $this->authorize('update', $obituary);

        $validated = $request->validate([
            'image_paths' => 'required|array',
            'image_paths.*' => 'required|string'
        ]);

        $imagePaths = $validated['image_paths'];
        $galleryImages = $obituary->gallery_images ?? [];
        $deletedCount = 0;

        foreach ($imagePaths as $imagePath) {
            // Check if image exists in gallery
            $imageIndex = array_search($imagePath, $galleryImages);
            if ($imageIndex !== false) {
                // Delete the file from storage
                if (Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }

                // Remove from gallery array
                unset($galleryImages[$imageIndex]);
                $deletedCount++;
            }
        }

        // Re-index array
        $galleryImages = array_values($galleryImages);

        // Update the database
        $obituary->update(['gallery_images' => $galleryImages]);

        return back()->with('success', "{$deletedCount} gallery image(s) removed successfully!");
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

    public function cleanupPage()
    {
        // Check policy authorization for managing files (admin only)
        $this->authorize('manageFiles', ObituaryPage::class);

        return Inertia::render('PagesGraveyard/Obituaries/Cleanup');
    }

    public function scanOrphanedFiles()
    {
        // Check policy authorization for managing files (admin only)
        $this->authorize('manageFiles', ObituaryPage::class);

        $orphanedFiles = [];
        $totalSize = 0;
        $obituaryFolders = ['obituaries/images', 'obituaries/gallery', 'obituaries/audio', 'qr-codes'];

        foreach ($obituaryFolders as $folder) {
            if (!Storage::disk('public')->exists($folder)) continue;

            $files = Storage::disk('public')->files($folder);

            foreach ($files as $file) {
                $isReferenced = ObituaryPage::where('profile_image', $file)
                    ->orWhere('audio_message', $file)
                    ->orWhereJsonContains('gallery_images', $file)
                    ->orWhere('qr_code_path', $file)
                    ->exists();

                if (!$isReferenced) {
                    $size = Storage::disk('public')->size($file);
                    $orphanedFiles[] = [
                        'path' => $file,
                        'name' => basename($file),
                        'size' => $size,
                        'size_human' => $this->formatFileSize($size),
                        'folder' => $folder,
                        'last_modified' => Storage::disk('public')->lastModified($file),
                    ];
                    $totalSize += $size;
                }
            }
        }

        return response()->json([
            'files' => $orphanedFiles,
            'count' => count($orphanedFiles),
            'total_size' => $totalSize,
            'total_size_human' => $this->formatFileSize($totalSize),
        ]);
    }

    public function executeCleanup(Request $request)
    {
        // Check policy authorization for managing files (admin only)
        $this->authorize('manageFiles', ObituaryPage::class);

        $validated = $request->validate([
            'files' => 'required|array',
            'files.*' => 'string'
        ]);

        $deletedFiles = [];
        $deletedSize = 0;

        foreach ($validated['files'] as $file) {
            if (Storage::disk('public')->exists($file)) {
                // Double-check the file is not referenced before deletion
                $isReferenced = ObituaryPage::where('profile_image', $file)
                    ->orWhere('audio_message', $file)
                    ->orWhereJsonContains('gallery_images', $file)
                    ->orWhere('qr_code_path', $file)
                    ->exists();

                if (!$isReferenced) {
                    $size = Storage::disk('public')->size($file);
                    Storage::disk('public')->delete($file);
                    $deletedFiles[] = $file;
                    $deletedSize += $size;
                }
            }
        }

        return response()->json([
            'success' => true,
            'deleted_count' => count($deletedFiles),
            'deleted_size' => $deletedSize,
            'deleted_size_human' => $this->formatFileSize($deletedSize),
            'message' => count($deletedFiles) > 0
                ? "Successfully cleaned up " . count($deletedFiles) . " orphaned files (" . $this->formatFileSize($deletedSize) . ")"
                : "No files were deleted"
        ]);
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
                    ->orWhere('qr_code_path', $file)
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

    private function formatFileSize(int $bytes): string
    {
        if ($bytes === 0) return '0 Bytes';
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
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
        // if (!Auth::user()->can('publish-obituary-page')) {
        //     abort(403, 'You do not have permission to publish obituary pages.');
        // }

        // Check policy authorization for publishing obituaries
        $this->authorize('update', $obituary);

        if ($obituary->isPublished()) {
            return back()->with('error', 'This obituary page is already published.');
        }

        // Check if payment is completed before allowing publication
        if (!$obituary->canBePublished()) {
            return back()->with('error', 'Cannot publish obituary page until payment is completed. Current payment status: ' . ($obituary->getPaymentStatus() ?? 'unknown'));
        }

        $obituary->publish(Auth::id());

        return back()->with('success', 'Obituary page has been published successfully.');
    }

    public function unpublish(ObituaryPage $obituary)
    {
        // Check if user has permission to unpublish obituary pages
        // if (!auth()->user()->can('unpublish-obituary-page')) {
        //     abort(403, 'You do not have permission to unpublish obituary pages.');
        // }

        // Check policy authorization for updating obituaries
        $this->authorize('update', $obituary);

        if (!$obituary->isPublished()) {
            return back()->with('error', 'This obituary page is not published.');
        }

        $obituary->unpublish();

        return back()->with('success', 'Obituary page has been unpublished successfully.');
    }

    public function rephraseText(Request $request)
    {
        try {
            $validated = $request->validate([
                'text' => 'required|string|max:2000',
                'field_type' => 'required|string|in:biography,favorite_memory,achievements,hobbies_interests,notes'
            ]);

            $apiKey = env('OPENAI_API_KEY');

            if (!$apiKey) {
                return response()->json([
                    'error' => 'OpenAI API key is not configured. Please contact the administrator.'
                ], 500);
            }

            // Test mode - return mock response if key starts with 'test-'
            if (str_starts_with($apiKey, 'test-')) {
                return response()->json([
                    'original_text' => $validated['text'],
                    'rephrased_text' => 'TEST MODE: This is a test rephrased version of your text: ' . $validated['text'],
                    'field_type' => $validated['field_type']
                ]);
            }

            // Hugging Face free model option
            if (str_starts_with($apiKey, 'hf-free')) {
                return $this->rephraseWithHuggingFace($validated);
            }

            // Simple rules only (completely free)
            if (str_starts_with($apiKey, 'simple-free')) {
                return $this->simpleRephrase($validated);
            }

            // Real OpenAI API call
            $fieldPrompts = [
                'biography' => 'Please rephrase this biography text to be more eloquent, respectful, and well-written while maintaining all personal details and the same meaning. Keep it suitable for an obituary page:',
                'favorite_memory' => 'Please rephrase this favorite memory text to be more touching, eloquent, and well-written while preserving all the personal details and emotional significance:',
                'achievements' => 'Please rephrase this achievements text to be more professionally written and respectful while maintaining all accomplishments and details:',
                'hobbies_interests' => 'Please rephrase this hobbies and interests text to be more eloquent and well-written while preserving all the personal interests and activities mentioned:',
                'notes' => 'Please rephrase this family notes and messages text to be more respectful, well-written, and appropriate for an obituary page while maintaining all important information:'
            ];

            $prompt = $fieldPrompts[$validated['field_type']] ?? $fieldPrompts['biography'];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->withOptions([
                'verify' => false, // Disable SSL verification for development
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-5',
                'input' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful assistant that specializes in writing respectful, eloquent obituary content. Always maintain the same meaning and all personal details while improving the writing quality.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt . "\n\n" . $validated['text']
                    ]
                ],
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'Failed to rephrase text. Please try again later.',
                    'api_status' => $response->status(),
                    'api_error' => $response->body()
                ], 500);
            }

            $data = $response->json();
            $rephrasedText = $data['choices'][0]['message']['content'] ?? '';

            if (empty($rephrasedText)) {
                return response()->json([
                    'error' => 'No rephrased text was generated. Please try again.'
                ], 500);
            }

            return response()->json([
                'original_text' => $validated['text'],
                'rephrased_text' => trim($rephrasedText),
                'field_type' => $validated['field_type']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while rephrasing the text. Please try again later.',
                'debug_message' => $e->getMessage(),
                'debug_line' => $e->getLine()
            ], 500);
        }
    }

    private function rephraseWithHuggingFace($validated)
    {
        try {
            $fieldPrompts = [
                'biography' => 'Rephrase this biography to be more eloquent and respectful:',
                'favorite_memory' => 'Rephrase this memory to be more touching and well-written:',
                'achievements' => 'Rephrase these achievements to be more professional:',
                'hobbies_interests' => 'Rephrase these hobbies and interests to be more eloquent:',
                'notes' => 'Rephrase this family message to be more respectful:'
            ];

            $prompt = $fieldPrompts[$validated['field_type']] ?? $fieldPrompts['biography'];
            $inputText = $prompt . " " . $validated['text'];

            // Using Hugging Face Inference API (free tier)
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->withOptions([
                'verify' => false,
            ])->timeout(30)->post('https://api-inference.huggingface.co/models/facebook/bart-large-cnn', [
                'inputs' => $inputText,
                'parameters' => [
                    'max_length' => 300,
                    'min_length' => 50,
                    'do_sample' => true,
                    'temperature' => 0.7
                ]
            ]);

            if (!$response->successful()) {
                // Fallback to simple text processing
                return $this->simpleRephrase($validated);
            }

            $data = $response->json();
            $rephrasedText = $data[0]['generated_text'] ?? '';

            if (empty($rephrasedText)) {
                return $this->simpleRephrase($validated);
            }

            return response()->json([
                'original_text' => $validated['text'],
                'rephrased_text' => trim($rephrasedText),
                'field_type' => $validated['field_type'],
                'method' => 'huggingface'
            ]);
        } catch (\Exception $e) {
            // Fallback to simple rephrase
            return $this->simpleRephrase($validated);
        }
    }

    private function simpleRephrase($validated)
    {
        // Simple rule-based text improvement (completely free)
        $text = $validated['text'];

        // Basic improvements
        $text = trim($text);
        $text = ucfirst($text); // Capitalize first letter
        $text = preg_replace('/\s+/', ' ', $text); // Remove extra spaces
        $text = str_replace(' i ', ' I ', $text); // Capitalize 'I'
        $text = preg_replace('/\. +([a-z])/', '. ' . strtoupper('$1'), $text); // Capitalize after periods

        // Add period if missing
        if (!str_ends_with($text, '.') && !str_ends_with($text, '!') && !str_ends_with($text, '?')) {
            $text .= '.';
        }

        // Field-specific improvements
        $improvements = [
            'biography' => [
                'was a' => 'was a beloved',
                'worked as' => 'served as',
                'very' => 'deeply',
                'good' => 'wonderful',
                'nice' => 'kind',
                'loved' => 'cherished'
            ],
            'favorite_memory' => [
                'remember' => 'fondly remember',
                'always' => 'will always',
                'happy' => 'joyful',
                'fun' => 'delightful'
            ],
            'achievements' => [
                'got' => 'received',
                'did' => 'accomplished',
                'won' => 'achieved'
            ],
            'hobbies_interests' => [
                'liked' => 'enjoyed',
                'loved' => 'was passionate about',
                'did' => 'pursued'
            ],
            'notes' => [
                'family' => 'loving family',
                'friends' => 'dear friends',
                'miss' => 'deeply miss'
            ]
        ];

        $fieldImprovements = $improvements[$validated['field_type']] ?? $improvements['biography'];

        foreach ($fieldImprovements as $from => $to) {
            $text = str_ireplace($from, $to, $text);
        }

        return response()->json([
            'original_text' => $validated['text'],
            'rephrased_text' => $text,
            'field_type' => $validated['field_type'],
            'method' => 'simple_rules'
        ]);
    }

    /**
     * Upgrade obituary from basic to premium plan
     */
    // public function upgradeToPremium(ObituaryPage $obituary)
    // {
    //     // Validate current status
    //     if ($obituary->service_type !== 'basic') {
    //         return back()->with('error', 'Only basic plans can be upgraded.');
    //     }

    //     if ($obituary->getPaymentStatus() !== 'completed') {
    //         return back()->with('error', 'Original payment must be completed before upgrading.');
    //     }

    //     // Simple approach: charge full premium amount
    //     $premiumAmount = 1500; // ₹1,500 for premium

    //     try {
    //         DB::beginTransaction();

    //         // Update service type immediately and enable premium features (simple approach)
    //         $obituary->update([
    //             'service_type' => 'premium',
    //             'allow_condolences' => true,
    //             'allow_memory_sharing' => true,
    //             'updated_by' => Auth::id()
    //         ]);

    //         // Create new payment record for the upgrade
    //         $payment = \Modules\Graveyard\Models\ObituaryPayment::create([
    //             'obituary_page_id' => $obituary->id,
    //             'amount' => $premiumAmount,
    //             'payment_status' => 'pending',
    //             'payment_reference' => 'UPG' . date('Ymd') . str_pad($obituary->id, 4, '0', STR_PAD_LEFT),
    //             'created_by' => Auth::id()
    //         ]);

    //         DB::commit();

    //         // Redirect to payment
    //         return redirect()->route('graveyard.obituaries.show', $obituary->uuid)
    //             ->with('success', 'Obituary upgraded to Premium! Premium features are now available. Please complete the payment.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('Failed to upgrade obituary: ' . $e->getMessage());
    //         return back()->with('error', 'Failed to upgrade obituary. Please try again.');
    //     }
    // }

    /**
     * Grant external member access to an obituary
     */
    public function grantExternalAccess(Request $request, ObituaryPage $obituary)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:obituary_managers,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $obituaryManager = \Modules\Graveyard\Models\ObituaryManager::create([
                'obituary_page_id' => $obituary->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'is_active' => true,
                'access_granted_by' => Auth::id(),
                'access_granted_at' => now(),
            ]);

            return back()->with('success', 'External access granted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to grant external access: ' . $e->getMessage());

            return back()->with('error', 'Failed to grant external access. Please try again.');
        }
    }

    /**
     * Revoke external member access
     */
    public function revokeExternalAccess(Request $request, ObituaryPage $obituary)
    {
        try {
            $obituary->obituaryManager()->delete();


            return back()->with('success', 'External access revoked successfully');
        } catch (\Exception $e) {
            Log::error('Failed to revoke external access: ' . $e->getMessage());


            return back()->with('error', 'Failed to revoke external access. Please try again.');
        }
    }

    /**
     * Toggle external member active status
     */
    public function toggleExternalAccess(Request $request, ObituaryPage $obituary)
    {
        try {
            $obituaryManager = $obituary->obituaryManager;

            if (!$obituaryManager) {
                return back()->with('error', 'No external member found');
            }

            $obituaryManager->update([
                'is_active' => !$obituaryManager->is_active,
                'blocked_until' => null, // Clear any blocks when toggling
            ]);

            $status = $obituaryManager->is_active ? 'enabled' : 'disabled';


            return back()->with('success', "External access {$status} successfully");
        } catch (\Exception $e) {
            Log::error('Failed to toggle external access: ' . $e->getMessage());


            return back()->with('error', 'Failed to toggle external access. Please try again.');
        }
    }

    /**
     * Reset external member password
     */
    public function resetExternalPassword(Request $request, ObituaryPage $obituary)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $obituaryManager = $obituary->obituaryManager;

            if (!$obituaryManager) {
                return back()->with('error', 'No external member found');
            }

            $obituaryManager->update([
                'password' => bcrypt($request->password),
                'login_attempts' => 0,
                'blocked_until' => null,
            ]);


            return back()->with('success', 'Password reset successfully');
        } catch (\Exception $e) {
            Log::error('Failed to reset external password: ' . $e->getMessage());


            return back()->with('error', 'Failed to reset password. Please try again.');
        }
    }
}
