<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PayrollPeriodRequest;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\PayrollComponent;
use App\Models\PayrollItem;
use App\Models\PayrollPeriod;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $periods = PayrollPeriod::where('tenant_id', $request->user()->tenant_id)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(15);

        return Inertia::render('Payroll/Index', [
            'periods' => $periods,
        ]);
    }

    public function createPeriod(PayrollPeriodRequest $request)
    {
        $validated = $request->validated();
        $tenantId = $request->user()->tenant_id;

        $exists = PayrollPeriod::where('tenant_id', $tenantId)
            ->where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['month' => 'Periode gajian untuk bulan ini sudah ada.']);
        }

        PayrollPeriod::create(array_merge($validated, ['tenant_id' => $tenantId]));

        ActivityLog::log('payroll_period.created', PayrollPeriod::class, null, "Payroll period {$validated['month']}/{$validated['year']} created");

        return back()->with('success', 'Periode gajian berhasil dibuat.');
    }

    public function show(PayrollPeriod $period)
    {
        $period->load(['payrolls.employee.department', 'payrolls.employee.position']);

        $stats = [
            'total_employees' => $period->payrolls->count(),
            'total_earnings' => $period->payrolls->sum('total_earnings'),
            'total_deductions' => $period->payrolls->sum('total_deductions'),
            'net_salary' => $period->payrolls->sum('net_salary'),
        ];

        return Inertia::render('Payroll/Show', [
            'period' => $period,
            'stats' => $stats,
        ]);
    }

    public function process(PayrollPeriod $period)
    {
        if ($period->status !== 'draft') {
            return back()->withErrors(['period' => 'Hanya periode draft yang bisa diproses.']);
        }

        $employees = Employee::where('is_active', true)->get();
        $components = PayrollComponent::where('is_active', true)->get();

        $period->update(['status' => 'processing']);

        foreach ($employees as $employee) {
            $existing = Payroll::where('employee_id', $employee->id)
                ->where('payroll_period_id', $period->id)
                ->exists();

            if ($existing) {
                continue;
            }

            $earnings = 0;
            $deductions = 0;
            $items = [];

            foreach ($components as $component) {
                $value = $component->default_value;

                if ($component->code === 'BPJS_K') {
                    $value = $employee->payroll_component_value ?? 0;
                    $value = $value * ($component->default_value / 100);
                } elseif ($component->code === 'BPJS_J') {
                    $value = $employee->payroll_component_value ?? 0;
                    $value = $value * ($component->default_value / 100);
                }

                if ($component->type === 'earning') {
                    $earnings += $value;
                } else {
                    $deductions += $value;
                }

                $items[] = [
                    'component_code' => $component->code,
                    'amount' => $value,
                    'type' => $component->type,
                    'description' => $component->name,
                ];
            }

            $netSalary = $earnings - $deductions;

            $payroll = Payroll::create([
                'employee_id' => $employee->id,
                'payroll_period_id' => $period->id,
                'basic_salary' => $employee->payroll_component_value ?? 0,
                'total_earnings' => $earnings,
                'total_deductions' => $deductions,
                'net_salary' => $netSalary,
                'status' => 'draft',
            ]);

            foreach ($items as $item) {
                PayrollItem::create(array_merge($item, [
                    'payroll_id' => $payroll->id,
                ]));
            }
        }

        $period->update([
            'status' => 'completed',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        ActivityLog::log('payroll_processed', PayrollPeriod::class, $period->id, "Payroll processed for {$period->month}/{$period->year}");

        return back()->with('success', 'Perhitungan gajian berhasil diproses.');
    }

    public function payslip(Payroll $payroll)
    {
        $payroll->load(['employee.department', 'employee.position', 'items.component', 'period']);

        return Inertia::render('Payroll/Payslip', [
            'payroll' => $payroll,
        ]);
    }

    public function exportPayroll(PayrollPeriod $period)
    {
        $payrolls = Payroll::with(['employee.department', 'employee.position'])
            ->where('payroll_period_id', $period->id)
            ->orderBy('employee_id')
            ->get();

        $filename = 'payroll_export_'.$period->year.'_'.str_pad((string) $period->month, 2, '0', STR_PAD_LEFT).'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payrolls) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['NIK', 'Nama', 'Jabatan', 'Gaji Pokok', 'Total Tunjangan', 'Total Potongan', 'Gaji Bersih']);

            foreach ($payrolls as $payroll) {
                fputcsv($file, [
                    $payroll->employee->nik ?? '-',
                    $payroll->employee->name ?? '-',
                    $payroll->employee->position->name ?? '-',
                    number_format((float) $payroll->basic_salary, 2, '.', ''),
                    number_format((float) $payroll->total_earnings, 2, '.', ''),
                    number_format((float) $payroll->total_deductions, 2, '.', ''),
                    number_format((float) $payroll->net_salary, 2, '.', ''),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
