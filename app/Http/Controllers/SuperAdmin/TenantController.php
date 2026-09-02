<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\SubscriptionPackage;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with('package');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('slug', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('subscription_status', $request->status);
        }

        $tenants = $query->latest()->paginate(15)->through(fn ($tenant) => [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'slug' => $tenant->slug,
            'subscription_status' => $tenant->subscription_status,
            'package' => $tenant->package?->name,
            'created_at' => $tenant->created_at->format('d M Y'),
        ]);

        return Inertia::render('SuperAdmin/Tenants/Index', [
            'tenants' => $tenants,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        $packages = SubscriptionPackage::where('is_active', true)->get();

        return Inertia::render('SuperAdmin/Tenants/Create', [
            'packages' => $packages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'package_id' => 'required|exists:subscription_packages,id',
        ]);

        $tenant = Tenant::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'package_id' => $validated['package_id'],
            'subscription_status' => 'active',
            'trial_ends_at' => now()->addDays(14),
            'settings' => [],
            'limits' => [
                'max_employees' => SubscriptionPackage::find($validated['package_id'])->max_employees,
                'max_storage_gb' => SubscriptionPackage::find($validated['package_id'])->max_storage_gb,
            ],
        ]);

        Domain::create([
            'domain' => "{$validated['slug']}.hrhub.id",
            'tenant_id' => $tenant->id,
        ]);

        User::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        return redirect()->route('super-admin.tenants.show', $tenant)
            ->with('success', 'Tenant berhasil dibuat.');
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('package', 'domains');

        $stats = [
            'total_users' => User::where('tenant_id', $tenant->id)->count(),
            'active_users' => User::where('tenant_id', $tenant->id)->where('is_active', true)->count(),
        ];

        return Inertia::render('SuperAdmin/Tenants/Show', [
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'subscription_status' => $tenant->subscription_status,
                'package' => $tenant->package,
                'trial_ends_at' => $tenant->trial_ends_at?->format('d M Y'),
                'settings' => $tenant->settings,
                'limits' => $tenant->limits,
                'created_at' => $tenant->created_at->format('d M Y'),
            ],
            'stats' => $stats,
            'domains' => $tenant->domains,
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'subscription_status' => 'sometimes|in:active,trial,suspended,terminated',
            'package_id' => 'sometimes|exists:subscription_packages,id',
        ]);

        $tenant->update($validated);

        if (isset($validated['package_id'])) {
            $package = SubscriptionPackage::find($validated['package_id']);
            $tenant->update([
                'limits' => [
                    'max_employees' => $package->max_employees,
                    'max_storage_gb' => $package->max_storage_gb,
                ],
            ]);
        }

        return back()->with('success', 'Tenant berhasil diperbarui.');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'Tenant berhasil dihapus.');
    }
}
