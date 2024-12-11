<?php

use App\Http\Controllers\VersionControlController;
use Illuminate\Support\Facades\Route;

Route::prefix('object')->group(function () {
    Route::post('/', [VersionControlController::class, 'store'])->name('object.store');
    Route::get('/get_all_records', [VersionControlController::class, 'index'])->name('object.get_all_records');
    Route::get('/{key}', [VersionControlController::class, 'show'])->name('object.show');
});
