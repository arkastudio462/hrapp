<?php

declare(strict_types=1);

use App\Http\Controllers\SuperAdmin\ActivityLogController;
use App\Http\Controllers\SuperAdmin\AnalyticsController;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\InvoiceController;
use App\Http\Controllers\SuperAdmin\LoginController;
use App\Http\Controllers\SuperAdmin\PackageController;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\SuperAdmin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('super-admin')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('super-admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('super-admin.login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('super-admin.logout');

    // Protected routes
    Route::middleware(['auth', 'role:super_admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('super-admin.dashboard');

        // Tenant management
        Route::get('/tenants', [TenantController::class, 'index'])->name('super-admin.tenants.index');
        Route::get('/tenants/create', [TenantController::class, 'create'])->name('super-admin.tenants.create');
        Route::post('/tenants', [TenantController::class, 'store'])->name('super-admin.tenants.store');
        Route::get('/tenants/{tenant}', [TenantController::class, 'show'])->name('super-admin.tenants.show');
        Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('super-admin.tenants.update');
        Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('super-admin.tenants.destroy');

        // Package management
        Route::get('/packages', [PackageController::class, 'index'])->name('super-admin.packages.index');
        Route::get('/packages/create', [PackageController::class, 'create'])->name('super-admin.packages.create');
        Route::post('/packages', [PackageController::class, 'store'])->name('super-admin.packages.store');
        Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('super-admin.packages.edit');
        Route::put('/packages/{package}', [PackageController::class, 'update'])->name('super-admin.packages.update');
        Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('super-admin.packages.destroy');
        Route::post('/packages/{package}/toggle', [PackageController::class, 'toggle'])->name('super-admin.packages.toggle');

        // Invoice management
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('super-admin.invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('super-admin.invoices.show');
        Route::put('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('super-admin.invoices.status');

        // User management
        Route::get('/users', [UserController::class, 'index'])->name('super-admin.users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('super-admin.users.show');
        Route::post('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('super-admin.users.toggle');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('super-admin.users.reset-password');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('super-admin.users.destroy');

        // Activity logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('super-admin.activity-logs.index');

        // Analytics
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('super-admin.analytics');
    });
});
