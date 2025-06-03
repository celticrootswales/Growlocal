<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GrowerController;
use App\Http\Controllers\GrowerCropPlanController;
use App\Http\Controllers\GrowerCommitmentController;
use App\Http\Controllers\GrowerWeeklyEstimateController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\TraceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DistributorController;
use App\Http\Controllers\CropOfferingController;
use App\Http\Controllers\AdminWeeklyPlanController;
use App\Http\Controllers\DistributorCropNeedController;
use App\Http\Controllers\DistributorWeeklyOverviewController;
use App\Http\Controllers\AdminDashboardController;

// Public welcome page
Route::get('/', function () {
    return view('welcome');
});

// Role-based dashboard redirect
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->hasRole('grower')) {
        return redirect()->route('grower.dashboard');
    } elseif ($user->hasRole('distributor')) {
        return redirect()->route('distributor.dashboard');
    }

    abort(403, 'Unauthorized access.');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Grower routes
Route::middleware(['auth', 'role:grower'])->prefix('grower')->name('grower.')->group(function () {
    Route::get('/dashboard', [DeliveryNoteController::class, 'dashboard'])->name('dashboard');

    // Delivery Notes
    Route::get('/delivery-notes', [DeliveryNoteController::class, 'index'])->name('notes.index');
    Route::get('/delivery-notes/create', [DeliveryNoteController::class, 'create'])->name('delivery-notes.create');
    Route::post('/delivery-notes', [DeliveryNoteController::class, 'store'])->name('delivery-notes.store');
    Route::post('/delivery-notes/{id}/deliver', [DeliveryNoteController::class, 'markDelivered'])->name('delivery-notes.markDelivered');
    Route::delete('/delivery-notes/{id}', [DeliveryNoteController::class, 'destroy'])->name('delivery-notes.delete');
    Route::get('/pdf/delivery-note/{id}', [DeliveryNoteController::class, 'generatePdf']);
    Route::get('/pdf/label/{id}', [DeliveryNoteController::class, 'generateLabel']);
    Route::post('/recall/{id}/acknowledge', [DeliveryNoteController::class, 'acknowledgeRecall'])->name('recall.acknowledge');

    // Crop Plan
    Route::get('/crop-plan', [GrowerCropPlanController::class, 'index'])->name('crop-plan.index');
    Route::put('/crop-plan/{id}', [GrowerCropPlanController::class, 'update'])->name('crop-plan.update');

    // Commitments
    Route::get('/commitments', [GrowerCommitmentController::class, 'index'])->name('commitments.index');
    Route::post('/commitments', [GrowerCommitmentController::class, 'store'])->name('commitments.store');

    // Weekly Estimates
    Route::get('/weekly-estimates', [GrowerWeeklyEstimateController::class, 'index'])->name('weekly-estimates.index');
    Route::post('/weekly-estimates', [GrowerWeeklyEstimateController::class, 'store'])->name('weekly-estimates.store');
});

// ✅ Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/notes', [AdminController::class, 'viewNotes'])->name('notes');
    Route::get('/recalls', [AdminController::class, 'manageRecalls'])->name('recalls');
    Route::post('/recall/{noteId}', [AdminController::class, 'issueRecall'])->name('recall');
    Route::delete('/recall/{noteId}', [AdminController::class, 'removeRecall'])->name('recall.remove');

    // Crop offering metrics dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');


    // Crop Offerings
    Route::get('/crop-offerings', [CropOfferingController::class, 'index'])->name('crop-offerings.index');
    Route::post('/crop-offerings', [CropOfferingController::class, 'store'])->name('crop-offerings.store');
    // Edit + Update + Delete for Crop Offerings
    Route::get('/crop-offerings/{id}/edit', [CropOfferingController::class, 'edit'])->name('crop-offerings.edit');
    Route::put('/crop-offerings/{id}', [CropOfferingController::class, 'update'])->name('crop-offerings.update');
    Route::delete('/crop-offerings/{id}', [CropOfferingController::class, 'destroy'])->name('crop-offerings.destroy');

    // Weekly Plans
    Route::get('/weekly-plans', [AdminWeeklyPlanController::class, 'index'])->name('weekly-plans.index');
    Route::post('/weekly-plans', [AdminWeeklyPlanController::class, 'store'])->name('weekly-plans.store');

    

    
});

// ✅ Distributor routes
Route::middleware(['auth', 'role:distributor'])->prefix('distributor')->name('distributor.')->group(function () {
    Route::get('/dashboard', [DistributorController::class, 'dashboard'])->name('dashboard');
    Route::get('/recalls', [DistributorController::class, 'recallList'])->name('recalls');
    Route::post('/recall/{noteId}', [DistributorController::class, 'issueRecall'])->name('recall');

    // Crop Plan
    Route::get('/crop-plan', [DistributorController::class, 'cropPlan'])->name('crop-plan.index');
    Route::post('/crop-plan', [DistributorController::class, 'storeCropPlan'])->name('crop-plan.store');
    Route::put('/crop-plan/{id}', [DistributorController::class, 'updateCropPlan'])->name('crop-plan.update');
    Route::delete('/crop-plan/{id}', [DistributorController::class, 'deleteCropPlan'])->name('crop-plan.delete');

    // Yearly Crop Needs
    Route::get('/crop-needs', [DistributorCropNeedController::class, 'index'])->name('crop-needs.index');
    Route::post('/crop-needs', [DistributorCropNeedController::class, 'store'])->name('crop-needs.store');

    // Weekly Overview
    Route::get('/weekly-overview', [DistributorWeeklyOverviewController::class, 'index'])->name('weekly-overview.index');
});

// Trace route
Route::get('/trace/{code}', [TraceController::class, 'show'])->name('trace.show');

// Auth routes
require __DIR__.'/auth.php';