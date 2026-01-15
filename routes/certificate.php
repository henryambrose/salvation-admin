<?php

use Modules\Members\Http\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;

// Certificate routes - DISABLED for now (redundant with Sacramental Records PDF download)
// The Baptism/Marriage/Death Records already have PDF download functionality
// Uncomment if formal certificate system with certificate numbers is needed in the future

// Route::middleware(['auth'])->prefix('certificates')->name('certificates.')->group(function () {
//   // Certificate listing and management
//   Route::get('/', [CertificateController::class, 'index'])->name('index');
//   Route::get('/generate', [CertificateController::class, 'generateMemberSelection'])->name('generate');
//   Route::get('/search-members', [CertificateController::class, 'searchMembers'])->name('search-members');
//   Route::get('/generate/{member}', [CertificateController::class, 'generate'])->whereNumber('member')->name('generate.member');

//   // API endpoints for wizard
//   Route::get('/api/members/{member}', [CertificateController::class, 'getMember'])->whereNumber('member')->name('api.member');
//   Route::get('/api/members/{member}/available-types', [CertificateController::class, 'getMemberAvailableTypes'])->whereNumber('member')->name('api.member.available-types');
//   Route::get('/api/members/{member}/certificates', [CertificateController::class, 'getMemberCertificates'])->whereNumber('member')->name('api.member.certificates');

//   // Certificate actions
//   Route::post('/', [CertificateController::class, 'store'])->name('store');
//   Route::post('/preview', [CertificateController::class, 'preview'])->name('preview');

//   // IMPORTANT: keep these last, and constrain the param to avoid conflicts:
//   Route::get('/{certificate}', [CertificateController::class, 'show'])->whereNumber('certificate')->name('show');
//   Route::get('/{certificate}/download', [CertificateController::class, 'download'])->whereNumber('certificate')->name('download');
//   Route::post('/{certificate}/reprint', [CertificateController::class, 'reprint'])->whereNumber('certificate')->name('reprint');
//   Route::post('/{certificate}/generate-pdf', [CertificateController::class, 'generatePdf'])->whereNumber('certificate')->name('generate-pdf');
// });