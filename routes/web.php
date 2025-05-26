<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GrowerController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\TraceController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

// Public welcome page
Route::get('/', function () {
    return view('welcome');
});

// Smart redirect dashboard (based on role)
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('grower')) {
        return redirect()->route('grower.dashboard');
    }

    abort(403, 'Unauthorized access.');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Grower-only routes
Route::middleware(['auth', 'role:grower'])->prefix('grower')->name('grower.')->group(function () {
    Route::get('/dashboard', [DeliveryNoteController::class, 'dashboard'])->name('dashboard');
    Route::get('/delivery-notes', [DeliveryNoteController::class, 'index'])->name('notes.index');
    Route::get('/delivery-notes/create', [DeliveryNoteController::class, 'create'])->name('delivery-notes.create');
    Route::post('/delivery-notes', [DeliveryNoteController::class, 'store'])->name('delivery-notes.store');
    Route::post('/delivery-notes/{id}/deliver', [DeliveryNoteController::class, 'markDelivered'])->name('delivery-notes.markDelivered');
    Route::delete('/delivery-notes/{id}', [DeliveryNoteController::class, 'destroy'])->name('delivery-notes.delete');
    Route::get('/pdf/delivery-note/{id}', [DeliveryNoteController::class, 'generatePdf']);
    Route::get('/pdf/label/{id}', [DeliveryNoteController::class, 'generateLabel']);
    Route::post('/recall/{id}/acknowledge', [DeliveryNoteController::class, 'acknowledgeRecall'])->name('recall.acknowledge');
});

// Public trace view
Route::get('/trace/{code}', [TraceController::class, 'show'])->name('trace.show');

// ✅ Admin-only routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/notes', [AdminController::class, 'viewNotes'])->name('notes'); // 👈 this one
    Route::get('/recalls', [AdminController::class, 'manageRecalls'])->name('recalls');
    Route::post('/recall/{noteId}', [AdminController::class, 'issueRecall'])->name('recall');
    Route::delete('/recall/{noteId}', [AdminController::class, 'removeRecall'])->name('recall.remove');
});

require __DIR__.'/auth.php';