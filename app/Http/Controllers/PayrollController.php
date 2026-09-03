<?php

declare(strict_types=1);

namespace App\Http\Controllers;

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
        $periods = PayrollPeriod::orderByDesc('year')
            ->orderByDesc('month')
            ->paginate(15);

        return Inertia::render('Payroll/Index', [
            'periods' => $periods,
        ]);
    }

    public function createPeriod(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
        ]);

        $exists = PayrollPeriod::where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['month' => 'Periode gajian untuk bulan ini sudah ada.']);
        }

        PayrollPeriod::create($validated);

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

        return back()->with('success', 'Perhitungan gajian berhasil diproses.');
    }

    public function payslip(Payroll $payroll)
    {
        $payroll->load(['employee.department', 'employee.position', 'items.component', 'period']);

        return Inertia::render('Payroll/Payslip', [
            'payroll' => $payroll,
        ]);
    }
}
