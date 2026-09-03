<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('subscription_status', 'active')->count();
        $trialTenants = Tenant::where('subscription_status', 'trial')->count();
        $totalUsers = User::count();
        $totalEmployees = Employee::count();

        $mrr = Invoice::where('status', 'paid')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount');

        $arr = Invoice::where('status', 'paid')
            ->where('created_at', '>=', now()->subYear())
            ->select(
                DB::raw('SUM(amount) as total'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $revenueByPackage = Invoice::where('status', 'paid')
            ->join('subscription_packages', 'invoices.package_id', '=', 'subscription_packages.id')
            ->select(
                'subscription_packages.name as package_name',
                DB::raw('SUM(invoices.amount) as total_revenue'),
                DB::raw('COUNT(invoices.id) as invoice_count')
            )
            ->groupBy('subscription_packages.name')
            ->get();

        $recentTenants = Tenant::with('package')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentInvoices = Invoice::with(['tenant', 'package'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return Inertia::render('SuperAdmin/Analytics', [
            'stats' => [
                'total_tenants' => $totalTenants,
                'active_tenants' => $activeTenants,
                'trial_tenants' => $trialTenants,
                'total_users' => $totalUsers,
                'total_employees' => $totalEmployees,
                'mrr' => $mrr,
            ],
            'revenueChart' => $arr,
            'revenueByPackage' => $revenueByPackage,
            'recentTenants' => $recentTenants,
            'recentInvoices' => $recentInvoices,
        ]);
    }
}
