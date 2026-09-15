<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PackageController extends Controller
{
    public function index()
    {
        $packages = SubscriptionPackage::withCount('tenants')->latest()->get();

        return Inertia::render('SuperAdmin/Packages/Index', [
            'packages' => $packages,
        ]);
    }

    public function create()
    {
        return Inertia::render('SuperAdmin/Packages/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_packages,slug',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_employees' => 'required|integer|min:1',
            'max_storage_gb' => 'required|integer|min:1',
            'features' => 'array',
            'is_active' => 'boolean',
        ]);

        $validated['features'] = $validated['features'] ?? [];
        $validated['is_active'] = $validated['is_active'] ?? true;

        SubscriptionPackage::create($validated);

        return redirect()->route('super-admin.packages.index')
            ->with('success', 'Paket berhasil dibuat.');
    }

    public function edit(SubscriptionPackage $package)
    {
        return Inertia::render('SuperAdmin/Packages/Edit', [
            'package' => $package,
        ]);
    }

    public function update(Request $request, SubscriptionPackage $package)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscription_packages,slug,'.$package->id,
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_employees' => 'required|integer|min:1',
            'max_storage_gb' => 'required|integer|min:1',
            'features' => 'array',
            'is_active' => 'boolean',
        ]);

        $validated['features'] = $validated['features'] ?? [];

        $package->update($validated);

        return redirect()->route('super-admin.packages.index')
            ->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(SubscriptionPackage $package)
    {
        if ($package->tenants()->count() > 0) {
            return back()->withErrors(['error' => 'Paket tidak bisa dihapus karena masih digunakan oleh tenant.']);
        }

        $package->delete();

        return redirect()->route('super-admin.packages.index')
            ->with('success', 'Paket berhasil dihapus.');
    }

    public function toggle(SubscriptionPackage $package)
    {
        $package->update(['is_active' => ! $package->is_active]);

        return back()->with('success', 'Status paket berhasil diubah.');
    }
}
