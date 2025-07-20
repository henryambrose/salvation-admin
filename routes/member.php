<?php

use App\Http\Controllers\Member\ProfileDetailsController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::redirect('member', '/member/index');

    Route::get('member/index', [MemberController::class, 'index'])->name('member.index');
    // Route::get('member/index-old', [MemberController::class, 'index_old'])->name('member.index-old');
    Route::resource('member', MemberController::class)->except(['index']);
    // Route::prefix('member')->group(function () {
    //     Route::resource('profile-details', ProfileDetailsController::class)->only(['create', 'store', 'edit', 'update'])->names([
    //         'create' => 'member.profile-details.create',
    //         'store'  => 'member.profile-details.store',
    //         'edit'   => 'member.profile-details.edit',
    //         'update' => 'member.profile-details.update',
    //     ]);
    //     // Route::resource('contact-details', ContactDetailsController::class)->only(['create', 'store', 'edit', 'update']);
    //     // Route::resource('education-details', EducationDetailsController::class)->only(['create', 'store', 'edit', 'update']);
    //     // Route::resource('address-details', AddressDetailsController::class)->only(['create', 'store', 'edit', 'update']);
    //     // Route::resource('family-details', FamilyDetailsController::class)->only(['create', 'store', 'edit', 'update']);
    //     // Route::resource('community-details', CommunityDetailsController::class)->only(['create', 'store', 'edit', 'update']);
    // });

    // memberDropdown
    Route::get('member/search-options', [MemberController::class, 'searchOptions'])
        ->name('member.search-options');

});

// Route::get('member/search-options', [MemberController::class, 'searchOptions']);
