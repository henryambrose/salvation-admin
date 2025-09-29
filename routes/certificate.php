<?php

use Modules\Members\Http\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;

// Certificate routes - clean and complete implementation
Route::middleware(['auth'])->prefix('certificates')->name('certificates.')->group(function () {
  // Certificate listing and management
  Route::get('/', [CertificateController::class, 'index'])->name('index');
  Route::get('/generate', [CertificateController::class, 'generateMemberSelection'])->name('generate');
  Route::get('/search-members', [CertificateController::class, 'searchMembers'])->name('search-members');
  Route::get('/generate/{member}', [CertificateController::class, 'generate'])->whereNumber('member')->name('generate.member');

  // API endpoints for wizard
  Route::get('/api/members/{member}', [CertificateController::class, 'getMember'])->whereNumber('member')->name('api.member');
  Route::get('/api/members/{member}/available-types', [CertificateController::class, 'getMemberAvailableTypes'])->whereNumber('member')->name('api.member.available-types');
  Route::get('/api/members/{member}/certificates', [CertificateController::class, 'getMemberCertificates'])->whereNumber('member')->name('api.member.certificates');
  Route::get('/api/templates', [CertificateController::class, 'getTemplatesByType'])->name('api.templates');

  // Certificate actions
  Route::post('/', [CertificateController::class, 'store'])->name('store');
  Route::post('/preview', [CertificateController::class, 'preview'])->name('preview');

  // Template management routes
  Route::get('/templates', [CertificateController::class, 'templateIndex'])->name('templates.index');
  Route::post('/templates', [CertificateController::class, 'templateStore'])->name('templates.store');
  Route::get('/templates/{template}', [CertificateController::class, 'templateShow'])->whereNumber('template')->name('templates.show');
  Route::put('/templates/{template}', [CertificateController::class, 'templateUpdate'])->whereNumber('template')->name('templates.update');
  Route::delete('/templates/{template}', [CertificateController::class, 'templateDestroy'])->whereNumber('template')->name('templates.destroy');
  Route::post('/templates/{template}/set-default', [CertificateController::class, 'setTemplateAsDefault'])->whereNumber('template')->name('templates.set-default');
  Route::get('/templates/{template}/preview', [CertificateController::class, 'templatePreview'])->whereNumber('template')->name('templates.preview');

  // IMPORTANT: keep these last, and constrain the param to avoid conflicts:
  Route::get('/{certificate}', [CertificateController::class, 'show'])->whereNumber('certificate')->name('show');
  Route::get('/{certificate}/download', [CertificateController::class, 'download'])->whereNumber('certificate')->name('download');
  Route::post('/{certificate}/reprint', [CertificateController::class, 'reprint'])->whereNumber('certificate')->name('reprint');
});