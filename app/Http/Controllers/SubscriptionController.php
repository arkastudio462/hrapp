<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\SubscriptionPackage;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $tenant = $request->user()->tenant;
        $tenant->load('package');

        $invoices = Invoice::where('tenant_id', $tenant->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return Inertia::render('Subscription/Index', [
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'subscription_status' => $tenant->subscription_status,
                'trial_ends_at' => $tenant->trial_ends_at,
                'package' => $tenant->package,
            ],
            'invoices' => $invoices,
        ]);
    }

    public function packages()
    {
        $packages = SubscriptionPackage::where('is_active', true)
            ->orderBy('price')
            ->get();

        return Inertia::render('Subscription/Packages', [
            'packages' => $packages,
        ]);
    }

    public function upgrade(Request $request, SubscriptionPackage $package)
    {
        $tenant = $request->user()->tenant;

        $invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'package_id' => $package->id,
            'amount' => $package->price,
            'status' => 'pending',
            'description' => "Upgrade ke {$package->name}",
        ]);

        return Inertia::render('Subscription/Payment', [
            'invoice' => $invoice->load('package'),
            'tenant' => $tenant,
        ]);
    }

    public function processPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer,credit_card,ewallet',
        ]);

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $validated['payment_method'],
        ]);

        $tenant = Tenant::find($invoice->tenant_id);
        $tenant->update([
            'package_id' => $invoice->package_id,
            'subscription_status' => 'active',
            'trial_ends_at' => null,
        ]);

        $package = SubscriptionPackage::find($invoice->package_id);
        $tenant->update([
            'limits' => [
                'max_employees' => $package->max_employees,
                'max_storage_gb' => $package->max_storage_gb,
            ],
        ]);

        return redirect()->route('subscription.index')
            ->with('success', 'Pembayaran berhasil. Subscription telah diaktifkan.');
    }

    public function cancel(Request $request)
    {
        $tenant = $request->user()->tenant;

        $tenant->update([
            'subscription_status' => 'cancelled',
        ]);

        return back()->with('success', 'Subscription telah dibatalkan.');
    }
}
