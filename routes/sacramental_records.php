<?php

use Illuminate\Support\Facades\Route;
use Modules\Members\Http\Controllers\BaptismRecordController;
use Modules\Members\Http\Controllers\MarriageRecordController;
use Modules\Members\Http\Controllers\DeathRecordController;

Route::middleware(['auth', 'verified'])->group(function () {

    // Baptism Records
    Route::resource('baptism-records', BaptismRecordController::class);
    Route::post('baptism-records/{id}/restore', [BaptismRecordController::class, 'restore'])
        ->name('baptism-records.restore');
    Route::get('baptism-records/{baptismRecord}/download-pdf', [BaptismRecordController::class, 'downloadPdf'])
        ->name('baptism-records.download-pdf');
    Route::get('baptism-records/member/{memberId}', [BaptismRecordController::class, 'getByMember'])
        ->name('baptism-records.by-member');

    // Marriage Records
    Route::resource('marriage-records', MarriageRecordController::class);
    Route::post('marriage-records/{id}/restore', [MarriageRecordController::class, 'restore'])
        ->name('marriage-records.restore');
    Route::get('marriage-records/{marriageRecord}/download-pdf', [MarriageRecordController::class, 'downloadPdf'])
        ->name('marriage-records.download-pdf');
    Route::get('marriage-records/member/{memberId}', [MarriageRecordController::class, 'getByMember'])
        ->name('marriage-records.by-member');

    // Death Records
    Route::resource('death-records', DeathRecordController::class);
    Route::post('death-records/{id}/restore', [DeathRecordController::class, 'restore'])
        ->name('death-records.restore');
    Route::get('death-records/{deathRecord}/download-pdf', [DeathRecordController::class, 'downloadPdf'])
        ->name('death-records.download-pdf');
    Route::get('death-records/member/{memberId}', [DeathRecordController::class, 'getByMember'])
        ->name('death-records.by-member');
});
