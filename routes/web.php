<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailFilterController;
use App\Http\Controllers\EmailFilterResultController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [EmailFilterController::class, 'show'])->name('dashboard');
    Route::post('email-filter/run', [EmailFilterController::class, 'process'])->name('email-filter.run');
    Route::get('email-filter/download/{token}', [EmailFilterController::class, 'download'])->name('email-filter.download');
    Route::get('email-filter/results', [EmailFilterResultController::class, 'index'])->name('email-filter.results');
});

require __DIR__.'/settings.php';
