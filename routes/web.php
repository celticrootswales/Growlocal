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
use App\Http\Controllers\AdminGrowerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GrowerWeeklyAllocationEstimateController;

// Public welcome page
Route::get('/', [HomeController::class, 'index']);

// Dashboard role redirect
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('admin')) return redirect()->route('admin.dashboard');
    if ($user->hasRole('grower')) return redirect()->route('grower.dashboard');
    if ($user->hasRole('distributor')) return redirect()->route('distributor.dashboard');
    abort(403, 'Unauthorized access.');
})->middleware(['auth', 'verified'])->name('dashboard');

// PDF & Traceability
Route::get('/delivery-notes/pdf/{id}', [DeliveryNoteController::class, 'generatePdf'])->name('delivery-notes.pdf')->middleware(['auth', 'role:admin|distributor|grower']);
Route::get('/trace/{code}', [TraceController::class, 'show'])->name('trace.show');

// ✅ Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ✅ Grower Routes
Route::middleware(['auth', 'role:grower'])->prefix('grower')->name('grower.')->group(function () {
    Route::get('/dashboard', [DeliveryNoteController::class, 'dashboard'])->name('dashboard');

    // Delivery Notes
    Route::get('/delivery-notes', [DeliveryNoteController::class, 'index'])->name('notes.index');
    Route::get('/delivery-notes/create', [DeliveryNoteController::class, 'create'])->name('delivery-notes.create');
    Route::post('/delivery-notes', [DeliveryNoteController::class, 'store'])->name('delivery-notes.store');
    Route::get('/delivery-notes/{id}/pdf', [DeliveryNoteController::class, 'generatePdf'])->name('delivery-notes.pdf.single');
    Route::get('/delivery-notes/{id}/label', [DeliveryNoteController::class, 'generateLabel'])->name('delivery-notes.label');
    Route::post('/delivery-notes/{id}/deliver', [DeliveryNoteController::class, 'markDelivered'])->name('delivery-notes.markDelivered');
    Route::delete('/delivery-notes/{id}', [DeliveryNoteController::class, 'destroy'])->name('delivery-notes.delete');
    Route::post('/recall/{id}/acknowledge', [DeliveryNoteController::class, 'acknowledgeRecall'])->name('recall.acknowledge');
    Route::get('/delivery-notes/offering-options/{distributor}', [DeliveryNoteController::class, 'getOfferingsByDistributor']);

    // Crop Plan
    Route::get('/crop-plan', [GrowerCropPlanController::class, 'index'])->name('crop-plan.index');
    Route::put('/crop-plan/{id}', [GrowerCropPlanController::class, 'update'])->name('crop-plan.update');

    // Commitments
    Route::resource('commitments', GrowerCommitmentController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);

    // Weekly Estimates
    Route::get('/weekly-estimates', [GrowerWeeklyEstimateController::class, 'index'])->name('weekly-estimates.index');
    Route::post('/weekly-estimates', [GrowerWeeklyAllocationEstimateController::class, 'store'])->name('weekly-estimates.store');
    Route::delete('/weekly-estimates/{estimate}', [GrowerWeeklyAllocationEstimateController::class, 'destroy'])
    ->name('weekly-estimates.destroy');
});

// ✅ Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Notes & Recalls
    Route::get('/notes', [AdminController::class, 'viewNotes'])->name('notes');
    Route::get('/recalls', [AdminController::class, 'manageRecalls'])->name('recalls');
    Route::post('/recall/{noteId}', [AdminController::class, 'issueRecall'])->name('recall');
    Route::delete('/recall/{noteId}', [AdminController::class, 'removeRecall'])->name('recall.remove');

    // Crop Offerings
    Route::resource('crop-offerings', CropOfferingController::class)->except(['create', 'show']);
    Route::post('/crop-offerings/{id}/submit', [CropOfferingController::class, 'submitToDistributors'])->name('offerings.submit');
    Route::post('/crop-offerings/{id}/push-to-needs', [CropOfferingController::class, 'pushToNeeds'])->name('offerings.push-to-needs');
    Route::patch('/crop-offerings/{id}/lock', [CropOfferingController::class, 'lock'])->name('crop-offerings.lock');
    Route::patch('/crop-offerings/{id}/unlock', [CropOfferingController::class, 'unlock'])->name('crop-offerings.unlock');
    Route::patch('/crop-offerings/lock-toggle/{year}', [CropOfferingController::class, 'toggleLockYear'])->name('crop-offerings.toggle-lock-year');

    // Weekly Plans
    Route::get('/weekly-plans', [AdminWeeklyPlanController::class, 'index'])->name('weekly-plans.index');
    Route::post('/weekly-plans', [AdminWeeklyPlanController::class, 'store'])->name('weekly-plans.store');
    Route::get('weekly-plans/plan/{offering}', [AdminWeeklyPlanController::class, 'plan'])->name('weekly-plans.plan');
    Route::post('weekly-plans/save/{grower}', [AdminWeeklyPlanController::class, 'save'])->name('weekly-plans.save');
    Route::post('weekly-plans/save-batch', [AdminWeeklyPlanController::class, 'saveBatch'])->name('weekly-plans.save.batch');
    Route::put('weekly-plans/allocation/{allocation}', [AdminWeeklyPlanController::class, 'updateAllocation'])->name('weekly-plans.allocation.update');
    Route::delete('weekly-plans/allocation/{allocation}', [AdminWeeklyPlanController::class, 'deleteAllocation'])->name('weekly-plans.allocation.delete');


    Route::get('grower/weekly-estimates', [GrowerWeeklyAllocationEstimateController::class, 'index'])->name('grower.weekly-estimates.index');
    Route::post('grower/weekly-estimates', [GrowerWeeklyAllocationEstimateController::class, 'store'])->name('grower.weekly-estimates.store');


    // Grower Management
    Route::resource('growers', AdminGrowerController::class)->except(['create', 'show']);
    Route::get('growers/{growerId}/commitments', [AdminGrowerController::class, 'showCommitments'])->name('growers.commitments');
    Route::post('/commitments/{id}/toggle-lock', [AdminGrowerController::class, 'toggleLock'])->name('commitments.toggle-lock');
});

// ✅ Distributor Routes
Route::middleware(['auth', 'role:distributor'])->prefix('distributor')->name('distributor.')->group(function () {
    Route::get('/dashboard', [DistributorController::class, 'dashboard'])->name('dashboard');

    // Recalls
    Route::get('/recalls', [DistributorController::class, 'recallList'])->name('recalls');
    Route::post('/recall/{noteId}', [DistributorController::class, 'issueRecall'])->name('recall');

    // Crop Plan
    Route::resource('crop-plan', DistributorController::class)->only(['index', 'store']);
    Route::put('/crop-plan/{id}', [DistributorController::class, 'updateCropPlan'])->name('crop-plan.update');
    Route::delete('/crop-plan/{id}', [DistributorController::class, 'deleteCropPlan'])->name('crop-plan.delete');

    // Crop Needs
    Route::get('/crop-needs', [DistributorCropNeedController::class, 'index'])->name('crop-needs.index');

    // Weekly Overview
    Route::get('/weekly-overview', [DistributorWeeklyOverviewController::class, 'index'])->name('weekly-overview.index');
});

// Laravel Auth Routes
require __DIR__.'/auth.php';