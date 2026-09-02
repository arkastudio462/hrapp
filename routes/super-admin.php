<?php

declare(strict_types=1);

use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\LoginController;
use App\Http\Controllers\SuperAdmin\TenantController;
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
    });
});
