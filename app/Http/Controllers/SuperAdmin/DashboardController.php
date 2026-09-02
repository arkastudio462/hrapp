<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'totalTenants' => Tenant::count(),
            'activeTenants' => Tenant::where('subscription_status', 'active')->count(),
            'trialTenants' => Tenant::where('subscription_status', 'trial')->count(),
            'totalUsers' => User::count(),
            'monthlyRevenue' => Invoice::where('status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount'),
            'pendingInvoices' => Invoice::where('status', 'pending')->count(),
        ];

        $recentTenants = Tenant::latest()
            ->take(5)
            ->get()
            ->map(fn ($tenant) => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'subscription_status' => $tenant->subscription_status,
                'created_at' => $tenant->created_at->format('d M Y'),
            ]);

        return Inertia::render('SuperAdmin/Dashboard', [
            'stats' => $stats,
            'recentTenants' => $recentTenants,
        ]);
    }
}
