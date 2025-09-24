<?php

namespace Modules\Members\Services;

use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Members\Models\FamilyPhoto;
use Modules\Members\Models\Member;

class FamilyPhotoService
{
    protected SecurityService $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Upload a family photo
     */
    public function uploadFamilyPhoto(string $familyNo, UploadedFile $file): array
    {
        try {
            DB::beginTransaction();

            // Validate the file - create a temporary request with the file
            $tempRequest = new \Illuminate\Http\Request();
            $tempRequest->files->set('photo', $file);
            $validation = $this->securityService->validateFileUpload($tempRequest, 'photo');

            if (!$validation['valid']) {
                return [
                    'success' => false,
                    'message' => $validation['error']
                ];
            }

            // Additional validation for family photos - only images
            if (!$this->isImageFile($file)) {
                return [
                    'success' => false,
                    'message' => 'Only image files (JPG, PNG, GIF) are allowed for family photos'
                ];
            }

            // Validate family exists
            if (!$this->familyExists($familyNo)) {
                return [
                    'success' => false,
                    'message' => 'Family not found'
                ];
            }

            // Check if family already has a photo
            $existingPhoto = FamilyPhoto::byFamily($familyNo)->active()->first();
            if ($existingPhoto) {
                // Replace existing photo
                $this->deleteFamilyPhoto($familyNo);
            }

            // Generate file path
            $filePath = $this->generateFilePath($familyNo, $file);

            // Store the file on the public disk so it's accessible via URL
            $storedPath = Storage::disk('public')->putFileAs(
                dirname($filePath),
                $file,
                basename($filePath)
            );

            if (!$storedPath) {
                throw new \Exception('Failed to store file');
            }

            // Create database record
            $familyPhoto = FamilyPhoto::create([
                'family_no' => $familyNo,
                'file_path' => $storedPath,
                'original_filename' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
                'is_active' => true,
            ]);

            DB::commit();
            return [
                'success' => true,
                'message' => 'Family photo uploaded successfully',
                'data' => [
                    'photo' => $familyPhoto,
                    'url' => $familyPhoto->photo_url
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Family photo upload failed', [
                'family_no' => $familyNo,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to upload family photo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a family photo
     */
    public function deleteFamilyPhoto(string $familyNo): array
    {
        try {
            $photo = FamilyPhoto::byFamily($familyNo)->active()->first();

            if (!$photo) {
                return [
                    'success' => false,
                    'message' => 'No family photo found'
                ];
            }

            // Soft delete the record (this will also delete the file via model event)
            $photo->delete();

            return [
                'success' => true,
                'message' => 'Family photo deleted successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Family photo deletion failed', [
                'family_no' => $familyNo,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to delete family photo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get family photo information
     */
    public function getFamilyPhoto(string $familyNo): ?FamilyPhoto
    {
        return FamilyPhoto::byFamily($familyNo)
            ->active()
            ->with('uploader')
            ->first();
    }

    /**
     * Get family photos for multiple families
     */
    public function getFamilyPhotos(array $familyNumbers): array
    {
        return FamilyPhoto::whereIn('family_no', $familyNumbers)
            ->active()
            ->with('uploader')
            ->get()
            ->keyBy('family_no')
            ->toArray();
    }

    /**
     * Check if a family exists
     */
    protected function familyExists(string $familyNo): bool
    {
        return Member::where('family_no', $familyNo)->exists();
    }

    /**
     * Check if the uploaded file is an image
     */
    protected function isImageFile(UploadedFile $file): bool
    {
        $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        $mimeType = $file->getMimeType();
        $extension = strtolower($file->getClientOriginalExtension());

        return in_array($mimeType, $allowedImageTypes) && in_array($extension, $allowedExtensions);
    }

    /**
     * Generate file path for family photo
     */
    protected function generateFilePath(string $familyNo, UploadedFile $file): string
    {
        $year = date('Y');
        $extension = $file->getClientOriginalExtension();
        $filename = 'family-photo.' . $extension;

        return "family-photos/{$year}/{$familyNo}/{$filename}";
    }

    /**
     * Clean up orphaned photos (photos without valid families)
     */
    public function cleanupOrphanedPhotos(): array
    {
        try {
            $orphanedPhotos = FamilyPhoto::whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('members')
                    ->whereColumn('members.family_no', 'family_photos.family_no');
            })->get();

            $deletedCount = 0;
            foreach ($orphanedPhotos as $photo) {
                $photo->delete();
                $deletedCount++;
            }

            return [
                'success' => true,
                'message' => "Cleaned up {$deletedCount} orphaned family photos",
                'deleted_count' => $deletedCount
            ];
        } catch (\Exception $e) {
            Log::error('Orphaned family photos cleanup failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to cleanup orphaned photos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get family photo statistics
     */
    public function getPhotoStatistics(): array
    {
        $totalPhotos = FamilyPhoto::active()->count();
        $totalFamilies = Member::distinct('family_no')->count();
        $familiesWithPhotos = FamilyPhoto::active()->distinct('family_no')->count();
        $coverage = $totalFamilies > 0 ? round(($familiesWithPhotos / $totalFamilies) * 100, 2) : 0;

        return [
            'total_photos' => $totalPhotos,
            'total_families' => $totalFamilies,
            'families_with_photos' => $familiesWithPhotos,
            'coverage_percentage' => $coverage,
        ];
    }
}
