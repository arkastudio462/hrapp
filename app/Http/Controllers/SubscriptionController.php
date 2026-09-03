<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\SubscriptionPackage;
use App\Models\Tenant;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function __construct(
        protected MidtransService $midtrans
    ) {}

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

        $invoiceNumber = 'INV-'.strtoupper(uniqid());
        $invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'package_id' => $package->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $package->price,
            'status' => 'pending',
            'description' => "Upgrade ke {$package->name}",
            'due_date' => now()->addDays(3),
        ]);

        try {
            $params = [
                'transaction_details' => [
                    'order_id' => $invoiceNumber,
                    'gross_amount' => (int) $package->price,
                ],
                'customer_details' => [
                    'first_name' => $tenant->name,
                    'email' => $request->user()->email,
                ],
                'item_details' => [
                    [
                        'id' => $package->id,
                        'price' => (int) $package->price,
                        'quantity' => 1,
                        'name' => $package->name,
                    ],
                ],
                'callbacks' => [
                    'finish' => route('subscription.index'),
                ],
            ];

            $snapToken = $this->midtrans->createTransaction($params);

            return Inertia::render('Subscription/Payment', [
                'invoice' => $invoice->load('package'),
                'tenant' => $tenant,
                'snap_token' => $snapToken['token'],
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Gagal memproses pembayaran: '.$e->getMessage()]);
        }
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

    public function callback(Request $request)
    {
        $notification = $request->all();

        $status = $this->midtrans->handleNotification($notification);

        $invoice = Invoice::where('invoice_number', $status['order_id'])->first();

        if (! $invoice) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        if ($status['transaction_status'] === 'settlement' || $status['transaction_status'] === 'capture') {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_method' => $status['payment_type'] ?? 'midtrans',
            ]);

            $tenant = Tenant::find($invoice->tenant_id);
            $package = SubscriptionPackage::find($invoice->package_id);

            if ($tenant && $package) {
                $tenant->update([
                    'package_id' => $package->id,
                    'subscription_status' => 'active',
                    'trial_ends_at' => null,
                    'limits' => [
                        'max_employees' => $package->max_employees,
                        'max_storage_gb' => $package->max_storage_gb,
                    ],
                ]);
            }
        } elseif ($status['transaction_status'] === 'cancel' || $status['transaction_status'] === 'expire') {
            $invoice->update(['status' => 'failed']);
        }

        return response()->json(['status' => 'ok']);
    }
}
