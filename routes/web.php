<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GrowerController;
use App\Http\Controllers\DeliveryNoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// General dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Grower-only routes
Route::middleware(['auth', 'role:grower'])->prefix('grower')->name('grower.')->group(function () {
    Route::get('/dashboard', [DeliveryNoteController::class, 'dashboard'])->name('dashboard');
    Route::post('/delivery-notes', [DeliveryNoteController::class, 'store'])->name('delivery-notes.store');
    Route::post('/delivery-notes/{id}/deliver', [DeliveryNoteController::class, 'markDelivered']);
    Route::get('/pdf/delivery-note/{id}', [DeliveryNoteController::class, 'generatePdf']);
    Route::get('/pdf/label/{id}', [DeliveryNoteController::class, 'generateLabel']);

    // 👇 Handles accidental GET requests to /grower/delivery-notes
    Route::get('/delivery-notes', function () {
        return redirect()->route('grower.dashboard');
    });
});

// ✅ Optional test route to check role middleware
Route::middleware(['auth', 'role:grower'])->get('/test-role', function () {
    return '✅ Role middleware is working for grower!';
});


require __DIR__.'/auth.php';