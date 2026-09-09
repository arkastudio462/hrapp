<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\PayrollComponent;
use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SetupWizardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        $steps = [
            ['id' => 1, 'name' => 'Company Profile', 'completed' => ! empty($tenant->settings['logo'])],
            ['id' => 2, 'name' => 'Work Settings', 'completed' => ! empty($tenant->settings['work_hours'])],
            ['id' => 3, 'name' => 'Departments & Positions', 'completed' => Department::where('tenant_id', $tenant->id)->exists()],
            ['id' => 4, 'name' => 'Add Employees', 'completed' => Employee::where('tenant_id', $tenant->id)->exists()],
            ['id' => 5, 'name' => 'Setup Payroll', 'completed' => PayrollComponent::where('tenant_id', $tenant->id)->exists()],
        ];

        $currentStep = $request->get('step', 1);

        $departments = Department::where('tenant_id', $tenant->id)->get();
        $positions = Position::where('tenant_id', $tenant->id)->get();

        return Inertia::render('SetupWizard/Index', [
            'tenant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
                'settings' => $tenant->settings,
            ],
            'steps' => $steps,
            'currentStep' => (int) $currentStep,
            'departments' => $departments,
            'positions' => $positions,
        ]);
    }

    public function updateStep1(Request $request)
    {
        $validated = $request->validate([
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'npwp' => 'nullable|string|max:20',
        ]);

        $user = $request->user();
        $tenant = $user->tenant;

        $tenant->update([
            'settings' => array_merge($tenant->settings ?? [], [
                'address' => $validated['address'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'npwp' => $validated['npwp'] ?? null,
            ]),
        ]);

        return redirect()->route('setup-wizard.index', ['step' => 2]);
    }

    public function updateStep2(Request $request)
    {
        $validated = $request->validate([
            'work_start' => 'required|string',
            'work_end' => 'required|string',
            'late_tolerance' => 'required|integer|min:0',
            'geofence_radius' => 'required|integer|min:50|max:500',
        ]);

        $user = $request->user();
        $tenant = $user->tenant;

        $tenant->update([
            'settings' => array_merge($tenant->settings ?? [], [
                'work_hours' => [
                    'start' => $validated['work_start'],
                    'end' => $validated['work_end'],
                ],
                'late_tolerance_minutes' => $validated['late_tolerance'],
                'geofence_radius' => $validated['geofence_radius'],
            ]),
        ]);

        return redirect()->route('setup-wizard.index', ['step' => 3]);
    }

    public function updateStep3(Request $request)
    {
        $validated = $request->validate([
            'departments' => 'required|array|min:1',
            'departments.*.name' => 'required|string|max:255',
            'positions' => 'required|array|min:1',
            'positions.*.name' => 'required|string|max:255',
            'positions.*.level' => 'required|integer|min:1',
        ]);

        $user = $request->user();
        $tenant = $user->tenant;

        foreach ($validated['departments'] as $dept) {
            Department::create([
                'tenant_id' => $tenant->id,
                'name' => $dept['name'],
            ]);
        }

        foreach ($validated['positions'] as $pos) {
            Position::create([
                'tenant_id' => $tenant->id,
                'name' => $pos['name'],
                'level' => $pos['level'],
            ]);
        }

        return redirect()->route('setup-wizard.index', ['step' => 4]);
    }

    public function updateStep4(Request $request)
    {
        $validated = $request->validate([
            'employees' => 'required|array|min:1',
            'employees.*.name' => 'required|string|max:255',
            'employees.*.email' => 'required|email',
            'employees.*.department_id' => 'required|exists:departments,id',
            'employees.*.position_id' => 'required|exists:positions,id',
            'employees.*.join_date' => 'required|date',
        ]);

        $user = $request->user();
        $tenant = $user->tenant;

        foreach ($validated['employees'] as $emp) {
            $nik = 'EMP'.str_pad(Employee::where('tenant_id', $tenant->id)->count() + 1, 5, '0', STR_PAD_LEFT);

            Employee::create([
                'tenant_id' => $tenant->id,
                'nik' => $nik,
                'name' => $emp['name'],
                'email_personal' => $emp['email'],
                'department_id' => $emp['department_id'],
                'position_id' => $emp['position_id'],
                'join_date' => $emp['join_date'],
                'status' => 'permanent',
                'is_active' => true,
            ]);
        }

        return redirect()->route('setup-wizard.index', ['step' => 5]);
    }

    public function updateStep5(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        $defaultComponents = [
            ['code' => 'BASIC', 'name' => 'Gaji Pokok', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 0],
            ['code' => 'THR', 'name' => 'Tunjangan Hari Raya', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 0],
            ['code' => 'TRANSPORT', 'name' => 'Tunjangan Transportasi', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 0],
            ['code' => 'MEAL', 'name' => 'Tunjangan Makan', 'type' => 'earning', 'calculation_type' => 'fixed', 'default_value' => 0],
            ['code' => 'OVERTIME', 'name' => 'Uang Lembur', 'type' => 'earning', 'calculation_type' => 'variable', 'default_value' => 0],
            ['code' => 'BPJS_K', 'name' => 'BPJS Kesehatan', 'type' => 'deduction', 'calculation_type' => 'percentage', 'default_value' => 1],
            ['code' => 'BPJS_J', 'name' => 'BPJS Ketenagakerjaan', 'type' => 'deduction', 'calculation_type' => 'percentage', 'default_value' => 0.5],
        ];

        foreach ($defaultComponents as $component) {
            PayrollComponent::create(array_merge($component, [
                'tenant_id' => $tenant->id,
                'is_active' => true,
            ]));
        }

        return redirect()->route('dashboard')
            ->with('success', 'Setup selesai! Selamat menggunakan HRapp.');
    }
}
