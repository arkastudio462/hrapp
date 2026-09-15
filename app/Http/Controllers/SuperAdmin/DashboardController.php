<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\SubscriptionPackage;
use App\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = now();

        $stats = [
            'totalTenants' => Tenant::count(),
            'activeTenants' => Tenant::where('subscription_status', 'active')->count(),
            'trialTenants' => Tenant::where('subscription_status', 'trial')->count(),
            'suspendedTenants' => Tenant::where('subscription_status', 'suspended')->count(),
            'totalUsers' => User::count(),
            'monthlyRevenue' => (float) Invoice::where('status', 'paid')
                ->whereYear('paid_at', $now->year)
                ->whereMonth('paid_at', $now->month)
                ->sum('amount'),
            'totalRevenue' => (float) Invoice::where('status', 'paid')->sum('amount'),
            'pendingInvoices' => Invoice::where('status', 'pending')->count(),
            'overdueInvoices' => Invoice::where('status', 'overdue')->count(),
        ];

        $revenueTrend = $this->monthlySeries(
            fn ($start, $end) => (float) Invoice::where('status', 'paid')
                ->whereBetween('paid_at', [$start, $end])
                ->sum('amount')
        );

        $tenantGrowth = $this->monthlySeries(
            fn ($start, $end) => Tenant::whereBetween('created_at', [$start, $end])->count()
        );

        $packageDistribution = SubscriptionPackage::withCount('tenants')
            ->orderBy('id')
            ->get()
            ->map(fn ($package) => [
                'name' => $package->name,
                'count' => $package->tenants_count,
            ])
            ->values();

        $recentTenants = Tenant::with('package')
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($tenant) => [
                'id' => $tenant->id,
                'name' => $tenant->name ?? $tenant->id,
                'subscription_status' => $tenant->subscription_status,
                'package' => $tenant->package?->name,
                'created_at' => $tenant->created_at->format('d M Y'),
            ]);

        $recentInvoices = Invoice::with(['tenant', 'package'])
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($invoice) => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'tenant_name' => $invoice->tenant?->name ?? $invoice->tenant?->id ?? '-',
                'amount' => (float) $invoice->amount,
                'status' => $invoice->status,
                'created_at' => $invoice->created_at->format('d M Y'),
            ]);

        return Inertia::render('SuperAdmin/Dashboard', [
            'stats' => $stats,
            'revenueTrend' => $revenueTrend,
            'tenantGrowth' => $tenantGrowth,
            'packageDistribution' => $packageDistribution,
            'recentTenants' => $recentTenants,
            'recentInvoices' => $recentInvoices,
        ]);
    }

    /**
     * Build a 12-month series (labels + values) using the provided aggregation callback.
     *
     * @return array{labels: array<int, string>, data: array<int, int|float>}
     */
    private function monthlySeries(Closure $query): array
    {
        $labels = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M');
            $data[] = $query($month->copy()->startOfMonth(), $month->copy()->endOfMonth());
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
