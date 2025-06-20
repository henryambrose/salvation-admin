
<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
  Route::get('/member/{id}/family-tree', [MemberController::class, 'showFamilyTree']);
});