<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['tenant', 'package']);

        if ($request->search) {
            $query->where('invoice_number', 'like', "%{$request->search}%")
                ->orWhereHas('tenant', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%");
                });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $invoices = $query->latest()->paginate(15)->through(fn ($invoice) => [
            'id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'tenant_name' => $invoice->tenant?->name ?? '-',
            'package_name' => $invoice->package?->name ?? '-',
            'amount' => $invoice->amount,
            'status' => $invoice->status,
            'due_date' => $invoice->due_date->format('d M Y'),
            'paid_at' => $invoice->paid_at?->format('d M Y H:i'),
            'payment_method' => $invoice->payment_method,
            'created_at' => $invoice->created_at->format('d M Y'),
        ]);

        $stats = [
            'total' => Invoice::count(),
            'pending' => Invoice::where('status', 'pending')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('amount'),
        ];

        return Inertia::render('SuperAdmin/Invoices/Index', [
            'invoices' => $invoices,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['tenant', 'package']);

        return Inertia::render('SuperAdmin/Invoices/Show', [
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => $invoice->amount,
                'status' => $invoice->status,
                'due_date' => $invoice->due_date->format('d M Y'),
                'paid_at' => $invoice->paid_at?->format('d M Y H:i'),
                'payment_method' => $invoice->payment_method,
                'created_at' => $invoice->created_at->format('d M Y'),
                'tenant' => [
                    'id' => $invoice->tenant?->id,
                    'name' => $invoice->tenant?->name ?? '-',
                ],
                'package' => [
                    'name' => $invoice->package?->name ?? '-',
                ],
            ],
        ]);
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,overdue',
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'paid') {
            $updateData['paid_at'] = now();
            $updateData['payment_method'] = $request->payment_method ?? 'manual';
        }

        $invoice->update($updateData);

        return back()->with('success', 'Status invoice berhasil diperbarui.');
    }
}
