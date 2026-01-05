<?php

use Modules\Members\Http\Controllers\BirthArchiveCertificateController;
use Modules\Members\Http\Controllers\MarriageArchiveCertificateController;
use Modules\Members\Http\Controllers\DeathArchiveCertificateController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('archive')->name('archive.')->group(function () {

    // Birth Archive Certificates
    Route::prefix('birth')->name('birth.')->group(function () {
        Route::redirect('/', '/archive/birth/index');
        Route::get('index', [BirthArchiveCertificateController::class, 'index'])->name('index');

        // Create
        Route::get('certificates/create', [BirthArchiveCertificateController::class, 'create'])->name('certificates.create');
        Route::post('certificates', [BirthArchiveCertificateController::class, 'store'])->name('certificates.store');

        // Read
        Route::get('certificates/{birthArchive}', [BirthArchiveCertificateController::class, 'show'])->name('certificates.show');
        Route::get('certificates/{birthArchive}/baptism', [BirthArchiveCertificateController::class, 'viewWithBaptism'])->name('certificates.baptism');

        // Update
        Route::get('certificates/{birthArchive}/edit', [BirthArchiveCertificateController::class, 'edit'])->name('certificates.edit');
        Route::put('certificates/{birthArchive}', [BirthArchiveCertificateController::class, 'update'])->name('certificates.update');
        Route::patch('certificates/{birthArchive}', [BirthArchiveCertificateController::class, 'update']);

        // Delete
        Route::delete('certificates/{birthArchive}', [BirthArchiveCertificateController::class, 'destroy'])->name('certificates.destroy');

        // Restore
        Route::post('{birthArchive}/restore', [BirthArchiveCertificateController::class, 'restore'])->name('restore');

        // Download
        Route::get('{birthArchive}/download', [BirthArchiveCertificateController::class, 'download'])->name('download');
    });

    // Marriage Archive Certificates
    Route::prefix('marriage')->name('marriage.')->group(function () {
        Route::redirect('/', '/archive/marriage/index');
        Route::get('index', [MarriageArchiveCertificateController::class, 'index'])->name('index');

        // Create
        Route::get('certificates/create', [MarriageArchiveCertificateController::class, 'create'])->name('certificates.create');
        Route::post('certificates', [MarriageArchiveCertificateController::class, 'store'])->name('certificates.store');

        // Read
        Route::get('certificates/{marriageArchive}', [MarriageArchiveCertificateController::class, 'show'])->name('certificates.show');
        Route::get('certificates/{marriageArchive}/marriage', [MarriageArchiveCertificateController::class, 'viewWithMarriage'])->name('certificates.marriage');

        // Update
        Route::get('certificates/{marriageArchive}/edit', [MarriageArchiveCertificateController::class, 'edit'])->name('certificates.edit');
        Route::put('certificates/{marriageArchive}', [MarriageArchiveCertificateController::class, 'update'])->name('certificates.update');
        Route::patch('certificates/{marriageArchive}', [MarriageArchiveCertificateController::class, 'update']);

        // Delete
        Route::delete('certificates/{marriageArchive}', [MarriageArchiveCertificateController::class, 'destroy'])->name('certificates.destroy');

        // Restore
        Route::post('{marriageArchive}/restore', [MarriageArchiveCertificateController::class, 'restore'])->name('restore');

        // Download
        Route::get('{marriageArchive}/download', [MarriageArchiveCertificateController::class, 'download'])->name('download');
    });

    // Death Archive Certificates
    Route::prefix('death')->name('death.')->group(function () {
        Route::redirect('/', '/archive/death/index');
        Route::get('index', [DeathArchiveCertificateController::class, 'index'])->name('index');

        // Create
        Route::get('certificates/create', [DeathArchiveCertificateController::class, 'create'])->name('certificates.create');
        Route::post('certificates', [DeathArchiveCertificateController::class, 'store'])->name('certificates.store');

        // Read
        Route::get('certificates/{deathArchive}', [DeathArchiveCertificateController::class, 'show'])->name('certificates.show');
        Route::get('certificates/{deathArchive}/death', [DeathArchiveCertificateController::class, 'viewWithDeath'])->name('certificates.death');

        // Update
        Route::get('certificates/{deathArchive}/edit', [DeathArchiveCertificateController::class, 'edit'])->name('certificates.edit');
        Route::put('certificates/{deathArchive}', [DeathArchiveCertificateController::class, 'update'])->name('certificates.update');
        Route::patch('certificates/{deathArchive}', [DeathArchiveCertificateController::class, 'update']);

        // Delete
        Route::delete('certificates/{deathArchive}', [DeathArchiveCertificateController::class, 'destroy'])->name('certificates.destroy');

        // Restore
        Route::post('{deathArchive}/restore', [DeathArchiveCertificateController::class, 'restore'])->name('restore');

        // Download
        Route::get('{deathArchive}/download', [DeathArchiveCertificateController::class, 'download'])->name('download');
    });
});
