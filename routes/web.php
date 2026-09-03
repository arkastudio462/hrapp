<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FaceAttendanceController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SetupWizardController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\TenantSettingsController;
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

    // Employee Management
    Route::resource('employees', EmployeeController::class)->except(['show']);

    // Department Management
    Route::resource('departments', DepartmentController::class)->except(['show']);

    // Position Management
    Route::resource('positions', PositionController::class)->except(['show']);

    // Attendance Management
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');
    Route::post('/attendances/qr/generate', [AttendanceController::class, 'generateQr'])->name('attendances.qr.generate');
    Route::post('/attendances/qr/scan', [AttendanceController::class, 'scanQr'])->name('attendances.qr.scan');

    // Face Attendance
    Route::get('/face-attendance', [FaceAttendanceController::class, 'index'])->name('face-attendance.index');
    Route::post('/face-attendance/register', [FaceAttendanceController::class, 'register'])->name('face-attendance.register');
    Route::post('/face-attendance/verify', [FaceAttendanceController::class, 'verify'])->name('face-attendance.verify');

    // Leave Management
    Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::post('/leaves/{leaveRequest}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leaveRequest}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    Route::delete('/leaves/{leaveRequest}', [LeaveController::class, 'destroy'])->name('leaves.destroy');

    // Payroll Management
    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/payroll', [PayrollController::class, 'createPeriod'])->name('payroll.create-period');
    Route::get('/payroll/{period}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::post('/payroll/{period}/process', [PayrollController::class, 'process'])->name('payroll.process');
    Route::get('/payroll/payslip/{payroll}', [PayrollController::class, 'payslip'])->name('payroll.payslip');

    // Tenant Settings
    Route::get('/settings', [TenantSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [TenantSettingsController::class, 'update'])->name('settings.update');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
