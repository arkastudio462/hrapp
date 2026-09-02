<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetupWizardController;
use App\Http\Controllers\TenantRegistrationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Central routes (no tenant)
Route::get('/', function () {
    return inertia('Welcome');
});

// Tenant Registration
Route::get('/register', [TenantRegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [TenantRegistrationController::class, 'register'])->name('register.post');

// Setup Wizard
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/setup-wizard', [SetupWizardController::class, 'index'])->name('setup-wizard.index');
    Route::post('/setup-wizard/step-1', [SetupWizardController::class, 'updateStep1'])->name('setup-wizard.step1');
    Route::post('/setup-wizard/step-2', [SetupWizardController::class, 'updateStep2'])->name('setup-wizard.step2');
    Route::post('/setup-wizard/step-3', [SetupWizardController::class, 'updateStep3'])->name('setup-wizard.step3');
    Route::post('/setup-wizard/step-4', [SetupWizardController::class, 'updateStep4'])->name('setup-wizard.step4');
    Route::post('/setup-wizard/step-5', [SetupWizardController::class, 'updateStep5'])->name('setup-wizard.step5');
});

// Tenant routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
