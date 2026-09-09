<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\SubscriptionPackage;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class TenantRegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        $packages = SubscriptionPackage::where('is_active', true)->get();

        return Inertia::render('Auth/Register', [
            'packages' => $packages,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|regex:/^[a-z0-9-]+$/|unique:tenants,slug',
            'admin_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'package_id' => 'required|exists:subscription_packages,id',
        ]);

        $package = SubscriptionPackage::find($validated['package_id']);

        $tenant = Tenant::create([
            'name' => $validated['company_name'],
            'slug' => $validated['subdomain'],
            'package_id' => $validated['package_id'],
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
            'settings' => [],
            'limits' => [
                'max_employees' => $package->max_employees,
                'max_storage_gb' => $package->max_storage_gb,
            ],
        ]);

        Domain::create([
            'domain' => "{$validated['subdomain']}.hrhub.id",
            'tenant_id' => $tenant->id,
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['admin_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'is_active' => true,
        ]);

        auth()->login($user);

        return redirect()->route('setup-wizard.index')
            ->with('success', 'Registrasi berhasil! Silakan lengkapi pengaturan perusahaan Anda.');
    }
}
